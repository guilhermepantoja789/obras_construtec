<script>
    function applyDateBrMask(input) {
        if (input.dataset.dateBrMask === '1') return;
        input.dataset.dateBrMask = '1';

        input.addEventListener('input', function (e) {
            const digits = e.target.value.replace(/\D/g, '').slice(0, 8);
            let formatted = digits;

            if (digits.length > 4) {
                formatted = digits.slice(0, 2) + '/' + digits.slice(2, 4) + '/' + digits.slice(4);
            } else if (digits.length > 2) {
                formatted = digits.slice(0, 2) + '/' + digits.slice(2);
            }

            e.target.value = formatted;
        });
    }

    function initDateBrFields(root = document) {
        root.querySelectorAll('.date-br-field').forEach(applyDateBrMask);
    }

    function brDateToIso(value) {
        if (!value || !String(value).trim()) return '';

        const match = String(value).trim().match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        if (!match) return null;

        const day = parseInt(match[1], 10);
        const month = parseInt(match[2], 10);
        const year = parseInt(match[3], 10);
        const date = new Date(year, month - 1, day);

        if (
            date.getFullYear() !== year ||
            date.getMonth() !== month - 1 ||
            date.getDate() !== day
        ) {
            return null;
        }

        return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    }

    function readDateFieldValue(input) {
        const value = input.value.trim();
        if (!value) return '';

        if (value.includes('/')) {
            return brDateToIso(value);
        }

        if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
            return value;
        }

        return null;
    }

    function prepareDateBrFieldsForSubmit(form) {
        for (const input of form.querySelectorAll('.date-br-field')) {
            const iso = readDateFieldValue(input);

            if (iso === null) {
                return { ok: false, input };
            }

            input.value = iso || '';
        }

        return { ok: true };
    }
</script>
