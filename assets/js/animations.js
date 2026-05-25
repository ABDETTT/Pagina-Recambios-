
(function () {
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.anim').forEach(function (el) {
            observer.observe(el);
        });
    });
}());
(function () {
    var navbar = document.querySelector('.navbar');
    if (!navbar) return;
    function onScroll() {
        navbar.classList.toggle('navbar-scrolled', window.scrollY > 60);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}());
(function () {
    function animateCounter(el) {
        var raw    = el.dataset.target || '0';
        var isEuro = raw.indexOf('.') !== -1;
        var target = parseFloat(raw);
        var dur    = 1200;
        var start  = null;
        function easeOut(t) { return 1 - Math.pow(1 - t, 3); }
        function step(ts) {
            if (!start) start = ts;
            var p = Math.min((ts - start) / dur, 1);
            var v = target * easeOut(p);
            el.textContent = isEuro
                ? v.toFixed(2).replace('.', ',') + ' €'
                : Math.floor(v).toString();
            if (p < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = isEuro
                    ? target.toFixed(2).replace('.', ',') + ' €'
                    : target.toString();
            }
        }
        requestAnimationFrame(step);
    }
    document.addEventListener('DOMContentLoaded', function () {
        var counters = document.querySelectorAll('[data-counter]');
        if (!counters.length) return;
        var obs = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        counters.forEach(function (el) { obs.observe(el); });
    });
}());
