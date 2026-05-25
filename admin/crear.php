<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/supabase_api.php'; 
if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] !== true) {
    header("Location: ../index.php");
    exit;
}
$res_coches = consultaSupabase("coches?select=*&order=marca.asc,modelo.asc");
$todos_coches = (!empty($res_coches) && !isset($res_coches['error'])) ? $res_coches : [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $precio_input = (float)($_POST['precio'] ?? 0);
    $stock_input  = (int)($_POST['stock'] ?? 10);
    if ($precio_input < 0 || $stock_input < 0) {
        $error = "El precio y el stock no pueden ser valores negativos.";
    } else {
        $imagen_url = subirImagenSupabase('imagen');
        if (!$imagen_url && !empty($_FILES['imagen']['name'])) {
            $error = "Error al subir la imagen. Verifica las políticas RLS en Supabase Storage.";
        }
        $nuevoProducto = [
            'nombre'       => trim($_POST['nombre'] ?? ''),
            'precio'       => $precio_input,
            'categoria_id' => (int)($_POST['categoria'] ?? 1),
            'descripcion'  => trim($_POST['descripcion'] ?? ''),
            'stock'        => $stock_input,
            'imagen_url'   => $imagen_url
        ];
        $respuesta = consultaSupabase("productos", "POST", $nuevoProducto);
        if (!isset($respuesta['error']) && !isset($respuesta['http_code'])) {
            $nuevo_id = $respuesta[0]['id'] ?? null;
            $coches_seleccionados = $_POST['coches'] ?? [];
            if ($nuevo_id && !empty($coches_seleccionados)) {
                $relaciones = [];
                foreach ($coches_seleccionados as $coche_id) {
                    $relaciones[] = [
                        'producto_id' => $nuevo_id,
                        'coche_id'    => (int)$coche_id
                    ];
                }
                consultaSupabase("coche_producto", "POST", $relaciones);
            }
            header("Location: index.php?creado=1");
            exit;
        } else {
            $error = "No se pudo crear el producto.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Producto | AutoStock</title>
    <link href="../assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container" style="max-width: 600px;">
    <div class="card shadow border-0 p-4">
        <h2 class="mb-4 titulo-oscuro">Registrar Nuevo Repuesto</h2>
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label fw-bold">Imagen del Producto</label>
                <input type="file" name="imagen" class="form-control" accept="image/*">
                <div class="form-text">Formatos permitidos: JPG, PNG, WEBP.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nombre del Repuesto</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Precio (€)</label>
                    <input type="number" step="0.01" min="0" name="precio" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Stock Inicial</label>
                    <input type="number" min="0" name="stock" class="form-control" value="10" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Categoría (ID)</label>
                <input type="number" name="categoria" class="form-control" placeholder="Ej: 1" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Vehículos Compatibles</label>
                <div class="border rounded p-3 bg-white" style="max-height: 200px; overflow-y: auto;">
                    <?php if (empty($todos_coches)): ?>
                        <span class="text-muted small">No hay coches registrados.</span>
                    <?php else: ?>
                        <div class="row">
                        <?php foreach ($todos_coches as $coche): ?>
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="coches[]" value="<?= $coche['id'] ?>" id="coche_<?= $coche['id'] ?>">
                                    <label class="form-check-label small" for="coche_<?= $coche['id'] ?>">
                                        <?= htmlspecialchars($coche['marca'] . ' ' . $coche['modelo']) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-text">Selecciona los modelos con los que este recambio es compatible.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"></textarea>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-naranja w-100 fw-bold">Guardar Producto</button>
                <a href="index.php" class="btn btn-outline-secondary w-100">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>