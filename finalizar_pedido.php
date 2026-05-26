<?php
require_once __DIR__ . '/includes/security.php';
initSecureSession();
require_once 'includes/supabase_api.php';
if (!isset($_SESSION['usuario_id'])) {
    header("Location: auth/login.php?redirect=finalizar_pedido.php");
    exit;
}
$carrito = $_SESSION['carrito'] ?? [];
if (empty($carrito)) {
    header("Location: carrito.php");
    exit;
}
$total = 0;
foreach ($carrito as $item) {
    $total += $item['precio'] * $item['cantidad'];
}
$pagina_titulo = "Finalizar Compra | AutoStock";
include_once 'includes/header.php';
$error_envio = $_SESSION['error_compra'] ?? null;
unset($_SESSION['error_compra']);
?>
<main class="container py-5" style="max-width: 960px;">
    <header class="text-center mb-5">
        <h2 class="fw-bold titulo-oscuro">Finalizar tu Pedido</h2>
        <p class="text-muted">Revisa tu compra, introduce la dirección y elige el pago</p>
    </header>

    <?php if ($error_envio): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="ph-fill ph-warning me-2"></i> <?= htmlspecialchars($error_envio) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="scripts/procesar_pedido.php" method="POST" id="form-checkout" novalidate>
        <?= csrfField() ?>
        <div class="row g-4 align-items-start">

            <!-- Resumen del pedido -->
            <aside class="col-md-5 col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 sticky-md-top" style="top: 80px;">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">
                        <i class="ph ph-receipt text-naranja me-2"></i>Resumen del Pedido
                    </h5>
                    <?php foreach ($carrito as $id => $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small"><?= $item['cantidad'] ?>× <?= htmlspecialchars($item['nombre']) ?></span>
                            <span class="fw-bold small"><?= number_format($item['precio'] * $item['cantidad'], 2) ?> €</span>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="fw-bold">Envío</span>
                        <span class="text-success fw-bold small"><i class="ph ph-check-circle me-1"></i>Gratuito</span>
                    </div>
                    <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                        <span class="h5 fw-bold mb-0">Total</span>
                        <span class="h5 fw-bold text-naranja mb-0"><?= number_format($total, 2) ?> €</span>
                    </div>
                </div>
            </aside>

            <!-- Dirección + Pago -->
            <div class="col-md-7 col-lg-8 d-flex flex-column gap-4">


                <!-- Dirección de envío -->
                <section class="card shadow-sm border-0 rounded-4 p-4">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">
                        <i class="ph ph-map-pin text-naranja me-2"></i>Dirección de Envío
                    </h5>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nombre y apellidos del destinatario <span class="text-danger">*</span></label>
                        <input type="text" name="destinatario" class="form-control"
                               placeholder="Nombre completo de quien recibe"
                               value="<?= htmlspecialchars(trim(($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? ''))) ?>"
                               required minlength="3">
                        <div class="invalid-feedback">Introduce el nombre del destinatario.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Dirección (calle, número, piso…) <span class="text-danger">*</span></label>
                        <input type="text" name="calle" class="form-control"
                               placeholder="Ej: Calle Mayor, 12, 3º Izq."
                               required minlength="5">
                        <div class="invalid-feedback">Introduce la dirección completa.</div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-4">
                            <label class="form-label fw-semibold small">Código Postal <span class="text-danger">*</span></label>
                            <input type="text" name="codigo_postal" class="form-control"
                                   placeholder="28001"
                                   pattern="^\d{5}$"
                                   maxlength="5"
                                   required>
                            <div class="invalid-feedback">5 dígitos.</div>
                        </div>
                        <div class="col-8">
                            <label class="form-label fw-semibold small">Ciudad <span class="text-danger">*</span></label>
                            <input type="text" name="ciudad" class="form-control"
                                   placeholder="Madrid"
                                   required>
                            <div class="invalid-feedback">Introduce la ciudad.</div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-semibold small">Provincia <span class="text-danger">*</span></label>
                            <input type="text" name="provincia" class="form-control"
                                   placeholder="Madrid"
                                   required>
                            <div class="invalid-feedback">Introduce la provincia.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Teléfono de contacto <span class="text-danger">*</span></label>
                            <input type="tel" name="telefono" class="form-control"
                                   placeholder="654 321 098"
                                   pattern="^[\d\s\+\-\(\)]{9,15}$"
                                   required>
                            <div class="invalid-feedback">Introduce un teléfono válido.</div>
                        </div>
                    </div>
                    <div class="mt-3 p-3 rounded-3 bg-light small text-muted d-flex align-items-start gap-2">
                        <i class="ph ph-truck text-naranja fs-5 flex-shrink-0"></i>
                        <span>Envío gratuito en 24–48h a península. Pedidos realizados antes de las 14:00h salen el mismo día.</span>
                    </div>
                </section>

                <!-- Método de pago -->
                <section class="card shadow-sm border-0 rounded-4 p-4">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">
                        <i class="ph ph-credit-card text-naranja me-2"></i>Método de Pago
                    </h5>
                    <div class="mb-4">
                        <div class="form-check border p-3 rounded-3 mb-2">
                            <input class="form-check-input ms-0 me-2" type="radio" name="forma_pago" id="tarjeta" value="Tarjeta de Crédito" checked>
                            <label class="form-check-label fw-bold" for="tarjeta">
                                <i class="ph ph-credit-card me-1"></i> Tarjeta de Crédito / Débito
                            </label>
                        </div>
                        <div class="form-check border p-3 rounded-3 mb-2">
                            <input class="form-check-input ms-0 me-2" type="radio" name="forma_pago" id="transferencia" value="Transferencia Bancaria">
                            <label class="form-check-label fw-bold" for="transferencia">
                                <i class="ph ph-bank me-1"></i> Transferencia Bancaria
                            </label>
                        </div>
                        <div class="form-check border p-3 rounded-3">
                            <input class="form-check-input ms-0 me-2" type="radio" name="forma_pago" id="paypal" value="PayPal">
                            <label class="form-check-label fw-bold" for="paypal">
                                <i class="ph ph-paypal-logo me-1"></i> PayPal
                            </label>
                        </div>
                    </div>
                    <p class="small text-muted mb-4">
                        <i class="ph-fill ph-shield-check text-success me-1"></i> Pago 100% seguro y cifrado. Al confirmar, procesaremos tu pedido de inmediato.
                    </p>
                    <button type="submit" class="btn btn-naranja btn-lg w-100 rounded-pill fw-bold shadow">
                        <i class="ph ph-lock-simple me-2"></i>Confirmar y Pagar — <?= number_format($total, 2) ?> €
                    </button>
                    <a href="carrito.php" class="btn btn-link w-100 mt-2 text-muted small text-decoration-none">
                        <i class="ph ph-arrow-left me-1"></i>Volver al carrito
                    </a>
                </section>

            </div>
        </div>
    </form>
</main>

<script>
document.getElementById('form-checkout').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('was-validated');
        this.querySelector(':invalid')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
        this.classList.add('was-validated');
    }
});
document.querySelector('input[name="codigo_postal"]')?.addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '').slice(0, 5);
});
</script>

<?php include_once 'includes/footer.php'; ?>
