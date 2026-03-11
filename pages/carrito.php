<?php
session_start();
require '../includes/db.php'; 

if (isset($_GET['vaciar'])) {
    unset($_SESSION['carrito']);
    header("Location: carrito.php");
    exit;
}

$carrito = $_SESSION['carrito'] ?? [];
$total = 0;

$pagina_titulo = "Mi Carrito | RecambiosPro";
include '../includes/header.php'; 
?>

<main class="container py-5" style="max-width: 900px;">
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-cart3 display-6 text-primary me-3"></i>
        <h2 class="mb-0 fw-bold">Mi Carrito</h2>
    </div>

    <?php if (empty($carrito)): ?>
        <div class="card shadow-sm border-0 text-center py-5">
            <div class="card-body">
                <i class="bi bi-bag-x display-1 text-muted opacity-50 mb-3"></i>
                <h4 class="fw-bold">Tu carrito está vacío</h4>
                <p class="text-muted mb-4">¡Explora nuestro catálogo y encuentra los mejores repuestos para tu vehículo!</p>
                <a href="catalogo.php" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold">
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
                            <th class="text-center py-3">Cantidad</th>
                            <th class="text-end py-3">Precio Unitario</th>
                            <th class="text-end pe-4 py-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carrito as $id => $item): ?>
                        <?php 
                            $subtotal = $item['precio'] * $item['cantidad']; 
                            $total += $subtotal; 
                        ?>
                        <tr>
                            <td class="ps-4 py-3 fw-bold text-dark">
                                <?php echo htmlspecialchars($item['nombre']); ?>
                            </td>
                            <td class="text-center py-3">
                                <span class="badge bg-secondary rounded-pill px-3 py-2 fs-6">
                                    <?php echo $item['cantidad']; ?>
                                </span>
                            </td>
                            <td class="text-end py-3 text-muted">
                                <?php echo number_format($item['precio'], 2); ?> €
                            </td>
                            <td class="text-end pe-4 py-3 fw-bold text-primary fs-5">
                                <?php echo number_format($subtotal, 2); ?> €
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
                    <h2 class="mb-3 text-primary fw-bold display-6"><?php echo number_format($total, 2); ?> €</h2>
                    
                    <a href="finalizar_pedido.php" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow">
                        <i class="bi bi-credit-card me-2"></i> Finalizar Compra
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>