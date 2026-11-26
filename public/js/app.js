(function () {
    'use strict';

    // Theme switcher (remembered in localStorage)
    document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var next = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', next);
            try { localStorage.setItem('theme', next); } catch (e) {}
        });
    });

    // Page loader
    var loader = document.getElementById('page-loader');
    function hideLoader() { if (loader) loader.classList.add('done'); }
    window.addEventListener('load', hideLoader);
    setTimeout(hideLoader, 4000);

    // Navbar background on scroll
    var nav = document.getElementById('siteNav');
    if (nav) {
        var onScroll = function () { nav.classList.toggle('scrolled', window.scrollY > 30); };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // Reveal-on-scroll animations
    var items = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
        }, { threshold: 0.1 });
        items.forEach(function (el) { io.observe(el); });
    } else {
        items.forEach(function (el) { el.classList.add('in'); });
    }

    // Loading state on forms
    document.querySelectorAll('form[data-loading]').forEach(function (form) {
        form.addEventListener('submit', function () {
            var btn = form.querySelector('button[type=submit]');
            if (!btn) return;
            var label = btn.querySelector('.btn-label'), busy = btn.querySelector('.btn-busy');
            if (label && busy) { label.classList.add('d-none'); busy.classList.remove('d-none'); }
            setTimeout(function () { btn.disabled = true; }, 0);
        });
    });

    // Confirm before delete
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm(form.getAttribute('data-confirm'))) e.preventDefault();
        });
    });

    // Image preview for file inputs
    document.querySelectorAll('input[type=file][data-preview]').forEach(function (input) {
        input.addEventListener('change', function () {
            var img = document.getElementById(input.getAttribute('data-preview'));
            var file = input.files && input.files[0];
            if (!img) return;
            if (file && file.type.indexOf('image/') === 0) {
                img.src = URL.createObjectURL(file);
                img.hidden = false;
            } else {
                img.hidden = true;
            }
        });
    });

    // Simple table filter (admin language content)
    document.querySelectorAll('[data-filter-table]').forEach(function (input) {
        input.addEventListener('input', function () {
            var q = input.value.toLowerCase();
            document.querySelectorAll(input.getAttribute('data-filter-table') + ' tbody tr').forEach(function (tr) {
                tr.hidden = q && tr.textContent.toLowerCase().indexOf(q) === -1 && !Array.from(tr.querySelectorAll('input')).some(function (i) {
                    return (i.value + ' ' + i.placeholder).toLowerCase().indexOf(q) !== -1;
                });
            });
        });
    });
})();
