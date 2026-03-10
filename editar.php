<?php
session_start();
require 'db.php';

// 1. SEGURIDAD: Si no es admin, fuera de aquí
if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] !== true) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? null;
$producto = null;

if ($id) {
    // 2. Obtener los datos actuales del producto
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch();
}

if (!$producto) {
    die("Producto no encontrado.");
}

// 3. Procesar la actualización cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];

    $sql = "UPDATE productos SET nombre = ?, precio = ?, descripcion = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$nombre, $precio, $descripcion, $id])) {
        header("Location: index.php?editado=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto | RecambiosPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow border-0 p-4">
            <h2 class="mb-4 text-primary">Editar Repuesto</h2>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre del Producto</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Precio (€)</label>
                    <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo $producto['precio']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="4"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success w-100 fw-bold">Guardar Cambios</button>
                    <a href="index.php" class="btn btn-outline-secondary w-100">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>