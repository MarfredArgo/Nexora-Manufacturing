<script>
    function isDesktop() {
        return window.matchMedia('(min-width: 1024px)').matches;
    }

    function toggleNav() {
        const sidenav = document.getElementById("mySidenav");
        const main = document.getElementById("main");
        const isOpen = sidenav.style.width === "250px";

        if (isOpen) {
            sidenav.style.width = "0";
            main.classList.remove("sidebar-open");
            if (isDesktop()) {
                main.style.marginLeft = "0";
            }
            localStorage.setItem('sidebarState', 'closed');
        } else {
            sidenav.style.width = "250px";
            main.classList.add("sidebar-open");
            if (isDesktop()) {
                main.style.marginLeft = "250px";
            }
            localStorage.setItem('sidebarState', 'open');
        }
    }

    function closeSidebarMobile() {
        if (isDesktop()) return;
        const sidenav = document.getElementById("mySidenav");
        const main = document.getElementById("main");
        sidenav.style.width = "0";
        main.classList.remove("sidebar-open");
        localStorage.setItem('sidebarState', 'closed');
    }

    function toggleAccordion(menuId, element) {
        const submenu = document.getElementById(menuId);
        element.classList.toggle("open");
        if (submenu.style.maxHeight) {
            submenu.style.maxHeight = null;
            localStorage.setItem('stockSubmenuState', 'closed');
        } else {
            submenu.style.maxHeight = submenu.scrollHeight + "px";
            localStorage.setItem('stockSubmenuState', 'open');
        }
    }

    function toggleProfileDropdown() {
        document.getElementById('profileDropdown').classList.toggle('open');
    }

    document.addEventListener('click', function(e) {
        const profDrop = document.getElementById('profileDropdown');
        if (profDrop && !e.target.closest('[onclick*="Profile"]') && !e.target.closest('#profileDropdown')) {
            profDrop.classList.remove('open');
        }
    });

    (function() {
        const sidenav = document.getElementById("mySidenav");
        const main = document.getElementById("main");
        const sidebarState = localStorage.getItem('sidebarState');

        if (sidebarState === 'open' && isDesktop()) {
            sidenav.style.width = "250px";
            main.style.marginLeft = "250px";
            main.classList.add("sidebar-open");
        } else {
            sidenav.style.width = "0";
            main.style.marginLeft = "0";
            main.classList.remove("sidebar-open");
        }

        let lastDesktop = isDesktop();
        window.addEventListener('resize', function() {
            const nowDesktop = isDesktop();
            if (nowDesktop !== lastDesktop) {
                lastDesktop = nowDesktop;
                if (!nowDesktop) {
                    sidenav.style.width = "0";
                    main.style.marginLeft = "0";
                    main.classList.remove("sidebar-open");
                } else if (localStorage.getItem('sidebarState') === 'open') {
                    sidenav.style.width = "250px";
                    main.style.marginLeft = "250px";
                    main.classList.add("sidebar-open");
                }
            }
        });

        const stockSubmenu = document.getElementById('stockSubmenu');
        const stockToggle = stockSubmenu.previousElementSibling;
        const submenuState = localStorage.getItem('stockSubmenuState');
        if (submenuState === 'open') {
            if (stockToggle) stockToggle.classList.add('open');
            stockSubmenu.style.transition = 'none';
            stockSubmenu.style.maxHeight = stockSubmenu.scrollHeight + "px";
            setTimeout(function() {
                stockSubmenu.style.transition = 'max-height 0.3s ease-out';
            }, 10);
        }
    })();
</script>


