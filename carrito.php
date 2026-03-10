<?php
session_start();
require 'db.php';

// Si el usuario hace clic en "Vaciar Carrito"
if (isset($_GET['vaciar'])) {
    unset($_SESSION['carrito']);
    header("Location: carrito.php");
    exit;
}

$carrito = $_SESSION['carrito'] ?? [];
$total = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Carrito | RecambiosPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-arrow-left"></i> Volver a la Tienda</a>
        </div>
    </nav>

    <div class="container" style="max-width: 800px;">
        <h2 class="mb-4"><i class="bi bi-cart3"></i> Mi Carrito</h2>

        <?php if (empty($carrito)): ?>
            <div class="alert alert-info text-center py-5">
                <h4>Tu carrito está vacío</h4>
                <p>¡Explora nuestro catálogo y encuentra lo que necesitas!</p>
                <a href="index.php" class="btn btn-primary mt-3">Ir al Catálogo</a>
            </div>
        <?php else: ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($carrito as $id => $item): ?>
                            <?php $subtotal = $item['precio'] * $item['cantidad']; $total += $subtotal; ?>
                            <tr>
                                <td class="align-middle fw-bold"><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td class="align-middle text-center"><?php echo $item['cantidad']; ?></td>
                                <td class="align-middle text-end"><?php echo number_format($item['precio'], 2); ?> €</td>
                                <td class="align-middle text-end fw-bold text-primary"><?php echo number_format($subtotal, 2); ?> €</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="?vaciar=1" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Vaciar Carrito</a>
                <div class="text-end">
                    <h4 class="mb-1">Total: <span class="text-primary fw-bold"><?php echo number_format($total, 2); ?> €</span></h4>
                    <a href="#" class="btn btn-success btn-lg mt-2 fw-bold shadow">
                        <i class="bi bi-credit-card"></i> Finalizar Compra
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>