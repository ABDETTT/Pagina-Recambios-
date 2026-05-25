<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/supabase_api.php';
$es_admin = isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true;
if (!$es_admin) {
    header("Location: ../index.php");
    exit;
}
$id = $_GET['id'] ?? null;
$producto = null;
if ($id) {
    $respuesta = consultaSupabase("productos?id=eq.$id&select=*");
    if (!empty($respuesta) && !isset($respuesta['error'])) {
        $producto = $respuesta[0];
    }
}
if (!$producto) {
    die("Producto no encontrado.");
}
$res_coches = consultaSupabase("coches?select=*&order=marca.asc,modelo.asc");
$todos_coches = (!empty($res_coches) && !isset($res_coches['error'])) ? $res_coches : [];
$cp_res = consultaSupabase("coche_producto?select=coche_id&producto_id=eq.$id");
$coches_asociados = (!empty($cp_res) && !isset($cp_res['error'])) ? array_column($cp_res, 'coche_id') : [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nueva_imagen_url = subirImagenSupabase('imagen');
    if (!$nueva_imagen_url && !empty($_FILES['imagen']['name'])) {
        $error_msg = "Error al subir la imagen. Verifica que el bucket 'imagenes_productos' exista y tenga políticas RLS para permitir la subida.";
    }
    $precio_input = (float)($_POST['precio'] ?? 0);
    if ($precio_input < 0) {
        $error_msg = "El precio no puede ser negativo.";
    } else {
        $datosActualizados = [
            'nombre'      => trim($_POST['nombre'] ?? ''),
            'precio'      => $precio_input,
            'descripcion' => trim($_POST['descripcion'] ?? '')
        ];
        if ($nueva_imagen_url) {
            $datosActualizados['imagen_url'] = $nueva_imagen_url;
        }
    $res = consultaSupabase("productos?id=eq.$id", "PATCH", $datosActualizados);
    if (!isset($res['error']) && !isset($res['http_code'])) {
        consultaSupabase("coche_producto?producto_id=eq.$id", "DELETE");
        $coches_seleccionados = $_POST['coches'] ?? [];
        if (!empty($coches_seleccionados)) {
            $relaciones = [];
            foreach ($coches_seleccionados as $coche_id) {
                $relaciones[] = [
                    'producto_id' => $id,
                    'coche_id'    => (int)$coche_id
                ];
            }
            consultaSupabase("coche_producto", "POST", $relaciones);
        }
        header("Location: index.php?editado=1");
        exit;
    } else {
        $error_msg = "Error al actualizar el producto.";
    }
}
}
$pagina_titulo = "Editar Producto | AutoStock";
require_once '../includes/header.php';
?>
<main class="bg-light py-5">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow border-0 p-4">
            <h2 class="mb-4 titulo-oscuro">Editar Repuesto</h2>
            <?php if(isset($error_msg)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($producto['imagen_url']): ?>
                    <div class="mb-3">
                        <label class="form-label d-block fw-bold text-muted small">Imagen Actual</label>
                        <img src="<?= htmlspecialchars($producto['imagen_url']) ?>" alt="Producto" class="img-thumbnail rounded mb-2" style="max-height: 100px;">
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Actualizar Imagen</label>
                    <input type="file" name="imagen" class="form-control" accept="image/*">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre del Producto</label>
                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Precio (€)</label>
                    <input type="number" step="0.01" min="0" name="precio" class="form-control" value="<?= htmlspecialchars($producto['precio']) ?>" required>
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
                                        <input class="form-check-input" type="checkbox" name="coches[]" value="<?= $coche['id'] ?>" id="coche_<?= $coche['id'] ?>" <?= in_array($coche['id'], $coches_asociados) ? 'checked' : '' ?>>
                                        <label class="form-check-label small" for="coche_<?= $coche['id'] ?>">
                                            <?= htmlspecialchars($coche['marca'] . ' ' . $coche['modelo']) ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-text">Actualiza los modelos con los que este recambio es compatible.</div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="4"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-naranja w-100 fw-bold">Guardar Cambios</button>
                    <a href="index.php" class="btn btn-outline-secondary w-100">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</main>
<?php require_once '../includes/footer.php'; ?>