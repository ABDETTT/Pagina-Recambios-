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
?>
<main class="container py-5" style="max-width: 800px;">
    <header class="text-center mb-5">
        <h2 class="fw-bold titulo-oscuro">Finalizar tu Pedido</h2>
        <p class="text-muted">Revisa tu compra y selecciona el método de pago</p>
    </header>
    <div class="row g-4">
        <section class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h5 class="fw-bold mb-4 border-bottom pb-2">Resumen del Pedido</h5>
                <?php foreach ($carrito as $id => $item): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small"><?= $item['cantidad'] ?>x <?= htmlspecialchars($item['nombre']) ?></span>
                        <span class="fw-bold small"><?= number_format($item['precio'] * $item['cantidad'], 2) ?> €</span>
                    </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between mt-3">
                    <span class="h5 fw-bold">Total</span>
                    <span class="h5 fw-bold text-naranja"><?= number_format($total, 2) ?> €</span>
                </div>
            </div>
        </section>
        <section class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-4 border-bottom pb-2">Método de Pago</h5>
                <form action="scripts/procesar_pedido.php" method="POST">
                    <?= csrfField() ?>
                    <div class="mb-4">
                        <div class="form-check border p-3 rounded-3 mb-2">
                            <input class="form-check-input ms-0 me-2" type="radio" name="forma_pago" id="tarjeta" value="Tarjeta de Crédito" checked>
                            <label class="form-check-label fw-bold" for="tarjeta">
                                <i class="ph ph-credit-card me-1"></i> Tarjeta de Crédito
                            </label>
                        </div>
                        <div class="form-check border p-3 rounded-3 mb-2">
                            <input class="form-check-input ms-0 me-2" type="radio" name="forma_pago" id="transferencia" value="Transferencia Bancaria">
                            <label class="form-check-label fw-bold" for="transferencia">
                                <i class="ph ph-bank me-1"></i> Transferencia
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
                        <i class="ph-fill ph-shield-check"></i> Pago 100% seguro. Al hacer clic en "Confirmar", procesaremos tu pedido de inmediato.
                    </p>
                    <button type="submit" class="btn btn-naranja btn-lg w-100 rounded-pill fw-bold shadow">
                        Confirmar y Pagar
                    </button>
                    <a href="carrito.php" class="btn btn-link w-100 mt-2 text-muted small text-decoration-none">Volver al carrito</a>
                </form>
            </div>
        </section>
    </div>
</main>
<?php include_once 'includes/footer.php'; ?>
