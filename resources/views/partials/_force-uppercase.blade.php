{{-- Forces text inputs/textareas to UPPERCASE as the user types.
     Safe by design: only plain text inputs and textareas are affected.
     Excluded automatically: password, email, url, number, tel, search,
     date/time, file, hidden, color and any field whose name looks
     sensitive (tokens, keys, emails, slugs, file paths, etc.).
     Opt out of an individual field with class="no-uppercase" or
     the attribute data-no-uppercase. --}}
<script>
    (function () {
        // Name fragments that must never be uppercased (case-insensitive match).
        var BLOCKED_NAMES = [
            'password', 'email', 'token', 'secret', 'api_key', 'app_key',
            'purchase_code', 'avatar', 'slug', 'url', 'link', 'image', 'hash'
        ];

        function isUppercasable(el) {
            if (!el || !el.matches) return false;
            // Only plain text inputs and textareas.
            if (!el.matches('input[type="text"], input:not([type]), textarea')) return false;
            // Explicit opt-out.
            if (el.hasAttribute('data-no-uppercase') || el.classList.contains('no-uppercase')) return false;

            var name = (el.getAttribute('name') || '').toLowerCase();
            for (var i = 0; i < BLOCKED_NAMES.length; i++) {
                if (name.indexOf(BLOCKED_NAMES[i]) > -1) return false;
            }
            return true;
        }

        // Delegated handler so dynamically added rows (DataTables, modals,
        // repeatable expense rows, etc.) are covered without re-binding.
        document.addEventListener('input', function (e) {
            var el = e.target;
            if (!isUppercasable(el)) return;

            var upper = el.value.toUpperCase();
            if (upper === el.value) return;

            var start = el.selectionStart;
            var end = el.selectionEnd;
            el.value = upper;
            try { el.setSelectionRange(start, end); } catch (_) {}
        }, true);
    })();
</script>
