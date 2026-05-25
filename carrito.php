<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/supabase_api.php';
$pagina_titulo = "Mi Carrito | AutoStock";
include_once 'includes/header.php';
if (isset($_GET['vaciar'])) {
    unset($_SESSION['carrito']);
    header("Location: carrito.php");
    exit;
}
$carrito = $_SESSION['carrito'] ?? [];
$total = 0;
?>
<main class="container py-5" style="max-width: 900px;">
    <header class="d-flex align-items-center mb-4">
        <i class="bi bi-cart3 display-6 text-naranja me-3"></i>
        <h2 class="mb-0 fw-bold">Mi Carrito</h2>
    </header>
    <?php if (isset($_SESSION['error_compra'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($_SESSION['error_compra']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_compra']); ?>
    <?php endif; ?>
    <?php if (empty($carrito)): ?>
        <div class="card shadow-sm border-0 text-center py-5">
            <div class="card-body">
                <i class="bi bi-bag-x display-1 text-muted opacity-50 mb-3"></i>
                <h4 class="fw-bold">Tu carrito está vacío</h4>
                <p class="text-muted mb-4">¡Explora nuestro catálogo y encuentra los mejores repuestos para tu vehículo!</p>
                <a href="catalogo.php" class="btn btn-naranja btn-lg rounded-pill px-4 fw-bold">
                    <i class="bi bi-search me-2"></i> Ir al Catálogo
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">Producto</th>
                            <th class="text-center py-3" style="width: 180px;">Cantidad</th>
                            <th class="text-end py-3">Precio Unitario</th>
                            <th class="text-end py-3">Subtotal</th>
                            <th class="text-center pe-4 py-3" style="width: 80px;">Quitar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carrito as $id => $item):
                            $subtotal = $item['precio'] * $item['cantidad'];
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td class="ps-4 py-3 fw-bold text-dark">
                                <?= htmlspecialchars($item['nombre']) ?>
                            </td>
                            <td class="text-center py-3">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <form action="scripts/actualizar_carrito.php" method="POST" class="m-0">
                                        <input type="hidden" name="id_producto" value="<?= $id ?>">
                                        <input type="hidden" name="accion" value="decrementar">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; padding: 0;" title="Disminuir cantidad">
                                            <i class="bi bi-dash fs-6 fw-bold"></i>
                                        </button>
                                    </form>
                                    <span class="badge bg-secondary rounded-pill px-3 py-2 fs-6" style="min-width: 45px;">
                                        <?= $item['cantidad'] ?>
                                    </span>
                                    <form action="scripts/actualizar_carrito.php" method="POST" class="m-0">
                                        <input type="hidden" name="id_producto" value="<?= $id ?>">
                                        <input type="hidden" name="accion" value="incrementar">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; padding: 0;" title="Aumentar cantidad">
                                            <i class="bi bi-plus fs-6 fw-bold"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="text-end py-3 text-muted">
                                <?= number_format($item['precio'], 2) ?> €
                            </td>
                            <td class="text-end py-3 fw-bold text-naranja fs-5">
                                <?= number_format($subtotal, 2) ?> €
                            </td>
                            <td class="text-center pe-4 py-3">
                                <form action="scripts/actualizar_carrito.php" method="POST" class="m-0" onsubmit="return confirm('¿Seguro que deseas eliminar este producto de tu carrito?');">
                                    <input type="hidden" name="id_producto" value="<?= $id ?>">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-2" title="Eliminar producto" style="line-height: 1;">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row align-items-center mb-5">
            <div class="col-md-6 mb-3 mb-md-0">
                <a href="?vaciar=1" class="btn btn-outline-danger rounded-pill fw-bold px-4">
                    <i class="bi bi-trash3 me-2"></i> Vaciar Carrito
                </a>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="bg-white p-4 rounded-3 shadow-sm d-inline-block w-100 text-end border">
                    <p class="text-muted mb-1">Total a pagar:</p>
                    <h2 class="mb-3 text-naranja fw-bold display-6"><?= number_format($total, 2) ?> €</h2>
                    <a href="finalizar_pedido.php" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow">
                        <i class="bi bi-credit-card me-2"></i> Finalizar Compra
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>
<?php include_once 'includes/footer.php'; ?>