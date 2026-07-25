@props(['name', 'value' => '', 'label' => 'Text'])

@php
    $id = 'rt_' . uniqid();
    // Convert newlines to <br> for legacy plain text values, but only if it doesn't already look like HTML
    $displayValue = (strpos($value, '<') !== false && strpos($value, '>') !== false) ? $value : str_replace("\n", '<br>', htmlspecialchars($value, ENT_QUOTES));
@endphp

<div>
    <div class="label-header">
        <span>{{ $label }}</span>
    </div>
    <div class="rt-container">
        <div class="rt-toolbar">
            <button type="button" class="rt-btn bold" onmousedown="event.preventDefault(); document.execCommand('bold', false, null); updateRt_{{ $id }}();" title="Bold">B</button>
            <button type="button" class="rt-btn italic" onmousedown="event.preventDefault(); document.execCommand('italic', false, null); updateRt_{{ $id }}();" title="Italic">I</button>
            <button type="button" class="rt-btn" onmousedown="event.preventDefault(); const url = prompt('Enter URL:'); if(url) { document.execCommand('createLink', false, url); updateRt_{{ $id }}(); }" title="Link"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg></button>
            <button type="button" class="rt-btn" onmousedown="event.preventDefault(); document.execCommand('insertUnorderedList', false, null); updateRt_{{ $id }}();" title="Bullet List"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg></button>
            <button type="button" class="rt-btn" onmousedown="event.preventDefault(); document.execCommand('insertOrderedList', false, null); updateRt_{{ $id }}();" title="Numbered List"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="10" y1="6" x2="21" y2="6"></line><line x1="10" y1="12" x2="21" y2="12"></line><line x1="10" y1="18" x2="21" y2="18"></line><path d="M4 6h1v4"></path><path d="M4 10h2"></path><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"></path></svg></button>
        </div>
        <div class="rt-editor" contenteditable="true" id="editor_{{ $id }}" oninput="updateRt_{{ $id }}()" onblur="updateRt_{{ $id }}()">{!! $displayValue !!}</div>
        <input type="hidden" name="{{ $name }}" id="hidden_{{ $id }}" value="{!! htmlspecialchars($displayValue, ENT_QUOTES, 'UTF-8') !!}">
    </div>
</div>

<script>
    function updateRt_{{ $id }}() {
        document.getElementById('hidden_{{ $id }}').value = document.getElementById('editor_{{ $id }}').innerHTML;
        if(typeof window.updateStaticPreview === 'function') {
            window.updateStaticPreview();
        }
    }
</script>
