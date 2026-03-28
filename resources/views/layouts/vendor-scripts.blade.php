<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>


<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/l10n/es.js') }}"></script>

<script>
// Auto-filter: forms with data-auto-filter submit on select change or text/date input (debounced)
document.addEventListener('DOMContentLoaded', function () {
    if (window.flatpickr) {
        if (window.flatpickr.l10ns && window.flatpickr.l10ns.es) {
            window.flatpickr.localize(window.flatpickr.l10ns.es);
        }

        document.querySelectorAll('.flatpickr-date').forEach(function (input) {
            if (input._flatpickr) return;

            window.flatpickr(input, {
                altInput: true,
                altFormat: input.dataset.altFormat || 'd/m/Y',
                dateFormat: input.dataset.dateFormat || 'Y-m-d',
                allowInput: true,
                locale: 'es',
                defaultDate: input.value || null,
            });
        });
    }

    document.querySelectorAll('[data-auto-filter]').forEach(function (form) {
        var timer;
        form.querySelectorAll('select').forEach(function (sel) {
            sel.addEventListener('change', function () { form.submit(); });
        });
        form.querySelectorAll('input[type="text"], input[type="number"], .flatpickr-date').forEach(function (inp) {
            inp.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () { form.submit(); }, 600);
            });
        });
    });
});
</script>
