document.addEventListener('DOMContentLoaded', function () {
    const banner       = document.getElementById('cookie-banner');
    const btnAceptar   = document.getElementById('accept-cookies');
    const btnRechazar  = document.getElementById('reject-cookies');
    const btnConfig    = document.getElementById('btn-configurar-cookies');
    const btnGuardar   = document.getElementById('save-cookie-prefs');
    const btnAceptarM  = document.getElementById('accept-all-modal');
    if (!banner) return;
    const COOKIE_KEY = 'rp_cookies_config';
    function getConfig() {
        try { return JSON.parse(localStorage.getItem(COOKIE_KEY)); } catch { return null; }
    }
    function saveConfig(cfg) {
        localStorage.setItem(COOKIE_KEY, JSON.stringify(cfg));
        document.cookie = 'cookies_accepted=true; max-age=' + (60 * 60 * 24 * 365) + '; path=/';
    }
    function hideBanner() {
        banner.classList.remove('show');
    }
    function applyConfig(cfg) {
        if (!cfg.analitica) {
            window['ga-disable-UA-XXXXXXX-X'] = true;
        }
    }
    if (!getConfig()) {
        setTimeout(() => banner.classList.add('show'), 1000);
    } else {
        applyConfig(getConfig());
    }
    btnAceptar?.addEventListener('click', function () {
        const cfg = { necesarias: true, analitica: true, personalizacion: true };
        saveConfig(cfg);
        applyConfig(cfg);
        hideBanner();
    });
    btnRechazar?.addEventListener('click', function () {
        const cfg = { necesarias: true, analitica: false, personalizacion: false };
        saveConfig(cfg);
        applyConfig(cfg);
        hideBanner();
        const chkA = document.getElementById('cookieAnalitica');
        const chkP = document.getElementById('cookiePersonalizacion');
        if (chkA) chkA.checked = false;
        if (chkP) chkP.checked = false;
    });
    btnConfig?.addEventListener('click', function () {
        const saved = getConfig();
        if (saved) {
            const chkA = document.getElementById('cookieAnalitica');
            const chkP = document.getElementById('cookiePersonalizacion');
            if (chkA) chkA.checked = !!saved.analitica;
            if (chkP) chkP.checked = !!saved.personalizacion;
        }
        const modal = new bootstrap.Modal(document.getElementById('modalCookies'));
        modal.show();
    });
    btnGuardar?.addEventListener('click', function () {
        const cfg = {
            necesarias:       true,
            analitica:        !!document.getElementById('cookieAnalitica')?.checked,
            personalizacion:  !!document.getElementById('cookiePersonalizacion')?.checked,
        };
        saveConfig(cfg);
        applyConfig(cfg);
        hideBanner();
        bootstrap.Modal.getInstance(document.getElementById('modalCookies'))?.hide();
    });
    btnAceptarM?.addEventListener('click', function () {
        const chkA = document.getElementById('cookieAnalitica');
        const chkP = document.getElementById('cookiePersonalizacion');
        if (chkA) chkA.checked = true;
        if (chkP) chkP.checked = true;
        const cfg = { necesarias: true, analitica: true, personalizacion: true };
        saveConfig(cfg);
        applyConfig(cfg);
        hideBanner();
        bootstrap.Modal.getInstance(document.getElementById('modalCookies'))?.hide();
    });
});
