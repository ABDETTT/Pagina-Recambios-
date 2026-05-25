<footer class="bg-azul-oscuro text-light pt-5 pb-3 mt-auto" style="border-top: 4px solid var(--naranja);">
    <div class="container">
      <div class="row gy-4 mb-4">
        <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
          <h5 class="text-white fw-bold mb-3 d-flex align-items-center">
            <i class="bi bi-gear-wide-connected text-naranja me-2 fs-4"></i> Auto<span class="text-azul-claro">Stock</span>
          </h5>
          <p class="small text-white-50 mb-4 pe-lg-4">
            Tu tienda online de confianza para repuestos de automóvil. Trabajamos con las mejores marcas para garantizar la máxima calidad y rendimiento para tu vehículo.
          </p>
          <div class="d-flex gap-2">
            <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" class="btn btn-outline-light border-0 bg-white bg-opacity-10 rounded-circle nav-link-custom"><i class="bi bi-facebook"></i></a>
            <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" class="btn btn-outline-light border-0 bg-white bg-opacity-10 rounded-circle nav-link-custom"><i class="bi bi-instagram"></i></a>
            <a href="https://www.youtube.com/" target="_blank" rel="noopener noreferrer" class="btn btn-outline-light border-0 bg-white bg-opacity-10 rounded-circle nav-link-custom"><i class="bi bi-youtube"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <h6 class="text-white fw-bold mb-3 text-uppercase" style="letter-spacing: 1px;">Tienda</h6>
          <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
            <li><a href="index.php" class="text-white-50 text-decoration-none nav-link-custom fs-6">Inicio</a></li>
            <li><a href="catalogo.php" class="text-white-50 text-decoration-none nav-link-custom fs-6">Catálogo Completo</a></li>
            <li><a href="catalogo.php?categoria=1" class="text-white-50 text-decoration-none nav-link-custom fs-6">Aceites y Líquidos</a></li>
            <li><a href="catalogo.php?categoria=2" class="text-white-50 text-decoration-none nav-link-custom fs-6">Frenos</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-6">
          <h6 class="text-white fw-bold mb-3 text-uppercase" style="letter-spacing: 1px;">Soporte</h6>
          <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
            <li><a href="<?= $path_prefix ?>contacto.php" class="text-white-50 text-decoration-none nav-link-custom fs-6">Contacto</a></li>
            <li><a href="<?= $path_prefix ?>aviso-legal.php#condiciones" class="text-white-50 text-decoration-none nav-link-custom fs-6">Envíos y Devoluciones</a></li>
            <li><a href="<?= $path_prefix ?>preguntas-frecuentes.php" class="text-white-50 text-decoration-none nav-link-custom fs-6">Preguntas Frecuentes</a></li>
            <li><a href="<?= $path_prefix ?>aviso-legal.php" class="text-white-50 text-decoration-none nav-link-custom fs-6">Aviso Legal</a></li>
            <li><a href="<?= $path_prefix ?>politica-privacidad.php" class="text-white-50 text-decoration-none nav-link-custom fs-6">Privacidad y RGPD</a></li>
          </ul>
        </div>
      </div>
      <hr class="border-light opacity-25 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
          <p class="small text-white-50 mb-0">&copy; <?= date('Y') ?> AutoStock. Todos los derechos reservados.</p>
        </div>
        <div class="col-md-6 text-center text-md-end">
          <div class="d-inline-flex gap-3 align-items-center opacity-75">
            <i class="bi bi-credit-card-fill fs-3 text-white"></i>
            <i class="bi bi-paypal fs-4 text-white"></i>
            <i class="bi bi-apple fs-4 text-white"></i>
            <span class="small text-white-50 fw-bold ms-2"><i class="bi bi-shield-lock-fill text-azul-claro me-1"></i>Pago Seguro</span>
          </div>
        </div>
      </div>
    </div>
</footer>
<div id="cookie-banner">
    <div class="container">
        <div class="d-flex align-items-start align-items-md-center">
            <i class="bi bi-info-circle-fill cookie-icon"></i>
            <p>
                Utilizamos cookies propias (necesarias para el carrito y la sesión) y de terceros para análisis de tráfico. Al hacer clic en "Aceptar Todo", consientes su uso. Lee nuestra <a href="<?= $path_prefix ?>aviso-legal.php#cookies">Política de Cookies</a> y <a href="<?= $path_prefix ?>politica-privacidad.php">Privacidad</a>.
            </p>
        </div>
        <div class="buttons">
            <button class="btn-settings" id="btn-configurar-cookies">Configurar</button>
            <button id="reject-cookies" class="btn-settings" style="margin-right:4px;">Solo necesarias</button>
            <button id="accept-cookies" class="btn-accept">Aceptar Todo</button>
        </div>
    </div>
</div>
<div class="modal fade" id="modalCookies" tabindex="-1" aria-labelledby="modalCookiesLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow">
      <div class="modal-header" style="background:var(--azul-oscuro,#192C76);">
        <h5 class="modal-title text-white fw-bold" id="modalCookiesLabel">
          <i class="bi bi-sliders me-2"></i>Configuración de Cookies
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <p class="small text-muted mb-4">Gestiona qué cookies quieres permitir. Las necesarias no se pueden desactivar porque son imprescindibles para el funcionamiento de la tienda.</p>
        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
          <div>
            <div class="fw-bold small">🔒 Cookies necesarias</div>
            <div class="text-muted" style="font-size:0.8rem;">Sesión, carrito, preferencias de cookies. Siempre activas.</div>
          </div>
          <div class="form-check form-switch ms-3 mt-1">
            <input class="form-check-input" type="checkbox" checked disabled style="cursor:not-allowed;">
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
          <div>
            <div class="fw-bold small">📊 Cookies analíticas</div>
            <div class="text-muted" style="font-size:0.8rem;">Nos ayudan a entender cómo se usa el sitio (Google Analytics). Los datos son anónimos.</div>
          </div>
          <div class="form-check form-switch ms-3 mt-1">
            <input class="form-check-input" type="checkbox" id="cookieAnalitica" checked>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="fw-bold small">🎯 Cookies de personalización</div>
            <div class="text-muted" style="font-size:0.8rem;">Recuerdan tus preferencias para mostrarte contenido relevante.</div>
          </div>
          <div class="form-check form-switch ms-3 mt-1">
            <input class="form-check-input" type="checkbox" id="cookiePersonalizacion" checked>
          </div>
        </div>
        <div class="mt-4 p-3 rounded-3 small" style="background:#f0f4ff;">
          <a href="<?= $path_prefix ?>aviso-legal.php#cookies" style="color:var(--azul-oscuro,#192C76);">
            <i class="bi bi-info-circle me-1"></i>Ver Política de Cookies completa
          </a>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 btn-sm" id="save-cookie-prefs">Guardar preferencias</button>
        <button type="button" class="btn rounded-pill px-3 btn-sm fw-bold" style="background:var(--naranja,#FF7403);color:#fff;" id="accept-all-modal">Aceptar todo</button>
      </div>
    </div>
  </div>
</div>
<?php
$path_prefix = "";
if (basename(dirname($_SERVER['PHP_SELF'])) === 'admin') {
    $path_prefix = "../";
}
?>
<script src="<?= $path_prefix ?>assets/js/animations.js"></script>
<script src="<?= $path_prefix ?>assets/js/cookies.js"></script>
<script src="<?= $path_prefix ?>assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>