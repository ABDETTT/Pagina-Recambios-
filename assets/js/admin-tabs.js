function adminTab(id) {
    document.querySelectorAll('#adminTabsContent .tab-pane').forEach(function (p) {
        p.style.display = 'none';
        p.classList.remove('active');
    });
    document.querySelectorAll('#adminTabs [data-tab]').forEach(function (b) {
        b.classList.remove('active');
    });
    var pane = document.getElementById(id);
    var btn  = document.querySelector('#adminTabs [data-tab="' + id + '"]');
    if (pane) { pane.style.display = 'block'; pane.classList.add('active'); }
    if (btn)  { btn.classList.add('active'); }
    sessionStorage.setItem('adminTab', id);
}
document.addEventListener('DOMContentLoaded', function () {
    var guardada = (sessionStorage.getItem('adminTab') || '').replace(/^#/, '');
    if (guardada && document.getElementById(guardada)) adminTab(guardada);
    document.querySelectorAll('form').forEach(function (f) {
        f.addEventListener('submit', function () {
            var a = document.querySelector('#adminTabs [data-tab].active');
            if (a) sessionStorage.setItem('adminTab', a.getAttribute('data-tab'));
        });
    });
});
