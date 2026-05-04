<?php
session_start();
require 'supabase_api.php';

$es_admin = isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true;

if (!$es_admin) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? null;
$producto = null;

if ($id) {
    $respuesta = consultaSupabase("productos?id=eq.$id&select=*");
    if (is_array($respuesta) && !empty($respuesta)) {
        $producto = $respuesta[0];
    }
}

if (!$producto) {
    die("Producto no encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datosActualizados = [
        'nombre'      => $_POST['nombre'],
        'precio'      => $_POST['precio'],
        'descripcion' => $_POST['descripcion']
    ];

    
    $endpoint = "productos?id=eq." . $id;
    
    $url = $supabaseUrl . "/rest/v1/" . $endpoint;
    $ch = curl_init($url);
    $headers = [
        "apikey: " . $supabaseKey,
        "Authorization: Bearer " . $supabaseKey,
        "Content-Type: application/json",
        "Prefer: return=representation"
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datosActualizados));

    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        header("Location: index.php?editado=1");
        exit;
    } else {
        $error_msg = "Error al actualizar el producto.";
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
            
            <?php if(isset($error_msg)): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre del Producto</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Precio (€)</label>
                    <input type="number" step="0.01" min="0" name="precio" class="form-control" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="4"><?php echo htmlspecialchars($producto['descripcion'] ?? ''); ?></textarea>
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