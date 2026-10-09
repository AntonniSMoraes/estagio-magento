define(['jquery'], function ($) {
    'use strict';
    var storageKey = 'noite_assombrada_modo',
        root = document.documentElement,
        active = false;

    try {
        active = window.localStorage.getItem(storageKey) === '1';
    } catch (error) {
    }

    function apply() {
        root.classList.toggle('modo-assombrado', active);
        $('[data-role="modo-assombrado"]').attr('aria-pressed', String(active));
    }

    apply();
    $(function () {
        apply();
        $(document).on('click.noiteAssombrada', '[data-role="modo-assombrado"]', function () {
            active = !active;
            apply();
            try {
                window.localStorage.setItem(storageKey, active ? '1' : '0');
            } catch (error) {
            }
        });
    });
});
