/**
 * Shared AJAX list loader for pagination, per-page, search, and filters.
 * Wrap a listing page with [data-ajax-list] and put replaceable HTML in [data-ajax-list-results].
 */
(function () {
  const root = document.querySelector('[data-ajax-list]');
  if (!root) return;

  const results = () => root.querySelector('[data-ajax-list-results]');
  let abortController = null;
  let searchTimer = null;

  function withAjaxParam(url) {
    const next = new URL(url, window.location.origin);
    next.searchParams.set('ajax', '1');
    return next;
  }

  function historyUrl(url) {
    const next = new URL(url, window.location.origin);
    next.searchParams.delete('ajax');
    return next.pathname + next.search + next.hash;
  }

  function setLoading(isLoading) {
    const target = results();
    if (!target) return;
    target.classList.toggle('opacity-50', isLoading);
    target.classList.toggle('pointer-events-none', isLoading);
    target.setAttribute('aria-busy', isLoading ? 'true' : 'false');
  }

  async function loadList(url, { push = true } = {}) {
    const target = results();
    if (!target) return;

    if (abortController) {
      abortController.abort();
    }
    abortController = new AbortController();

    const requestUrl = withAjaxParam(url);
    setLoading(true);

    try {
      const response = await fetch(requestUrl.toString(), {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          Accept: 'text/html',
        },
        signal: abortController.signal,
        credentials: 'same-origin',
      });

      if (!response.ok) {
        throw new Error('Failed to load list');
      }

      target.innerHTML = await response.text();

      if (push) {
        history.pushState({ ajaxList: true }, '', historyUrl(url));
      }
    } catch (error) {
      if (error.name !== 'AbortError') {
        window.location.href = historyUrl(url);
      }
    } finally {
      setLoading(false);
    }
  }

  function urlFromForm(form) {
    const action = new URL(form.getAttribute('action') || window.location.href, window.location.origin);
    const current = new URL(window.location.href);
    const data = new FormData(form);

    // Reset query, then apply form fields
    [...action.searchParams.keys()].forEach((key) => action.searchParams.delete(key));

    data.forEach((value, key) => {
      if (String(value).trim() !== '') {
        action.searchParams.set(key, String(value));
      }
    });

    if (!action.searchParams.has('per_page') && current.searchParams.has('per_page')) {
      action.searchParams.set('per_page', current.searchParams.get('per_page'));
    }

    action.searchParams.delete('page');
    action.searchParams.delete('ajax');
    return action;
  }

  root.addEventListener('click', (event) => {
    const link = event.target.closest('.pagination-wrap a[href]');
    if (!link || !root.contains(link)) return;
    event.preventDefault();
    loadList(link.href);
  });

  root.addEventListener('change', (event) => {
    const el = event.target;

    if (el.matches('.js-per-page')) {
      const url = new URL(window.location.href);
      url.searchParams.set('per_page', el.value);
      url.searchParams.delete('page');
      loadList(url.toString());
      return;
    }

    if (el.matches('select[name="sort"], select[name="department"]')) {
      const form = el.closest('form');
      if (!form || !root.contains(form)) return;
      loadList(urlFromForm(form).toString());
    }
  });

  root.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !root.contains(form)) return;
    event.preventDefault();
    loadList(urlFromForm(form).toString());
  });

  root.addEventListener('input', (event) => {
    const input = event.target;
    if (!input.matches('input[name="search"]')) return;
    const form = input.closest('form');
    if (!form || !root.contains(form)) return;

    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
      loadList(urlFromForm(form).toString());
    }, 350);
  });

  window.addEventListener('popstate', () => {
    loadList(window.location.href, { push: false });
  });
})();
