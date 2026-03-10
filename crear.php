<?php
session_start();
require 'db.php';

// Seguridad: Solo admin puede añadir productos
if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] !== true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];
    $descripcion = $_POST['descripcion'];
    $stock = (int)$_POST['stock']; // Capturamos el stock del formulario

    // Añadimos 'stock' y 'descripcion' a la consulta SQL
    $sql = "INSERT INTO productos (nombre, precio, categoria_id, descripcion, stock) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$nombre, $precio, $categoria, $descripcion, $stock])) {
        header("Location: index.php?creado=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Producto | RecambiosPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container" style="max-width: 600px;">
    <div class="card shadow border-0 p-4">
        <h2 class="mb-4 text-primary">Registrar Nuevo Repuesto</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Nombre del Repuesto</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Precio (€)</label>
                    <input type="number" step="0.01" name="precio" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Stock Inicial</label>
                    <input type="number" name="stock" class="form-control" value="10" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Categoría (ID)</label>
                <input type="number" name="categoria" class="form-control" placeholder="Ej: 1" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"></textarea>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary w-100 fw-bold">Guardar Producto</button>
                <a href="index.php" class="btn btn-outline-secondary w-100">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>