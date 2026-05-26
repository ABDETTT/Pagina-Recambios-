<?php
require_once __DIR__ . '/../includes/security.php';
initSecureSession();
require_once '../includes/supabase_api.php';
require_once '../includes/config.php';
$es_admin = isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true;
if (!$es_admin) {
    header("Location: ../index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrfCheck()) {
    header("Location: index.php");
    exit;
}
$id = (int)($_POST['id'] ?? 0);
if (!$id) {
    header("Location: index.php");
    exit;
}
$serviceKey = SUPABASE_SERVICE_KEY;
$supabaseUrl = SUPABASE_URL;
function adminDelete($supabaseUrl, $serviceKey, $endpoint) {
    $ch = curl_init($supabaseUrl . '/rest/v1/' . $endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . $serviceKey,
        'Authorization: Bearer ' . $serviceKey,
        'Content-Type: application/json',
        'Prefer: return=minimal',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $code;
}
adminDelete($supabaseUrl, $serviceKey, "coche_producto?producto_id=eq.$id");
adminDelete($supabaseUrl, $serviceKey, "detalle_pedido?producto_id=eq.$id");
$code = adminDelete($supabaseUrl, $serviceKey, "productos?id=eq.$id");
if ($code < 400) {
    $_SESSION['admin_msg'] = ['tipo' => 'success', 'texto' => "Producto #$id eliminado correctamente."];
} else {
    $_SESSION['admin_msg'] = ['tipo' => 'danger', 'texto' => "No se pudo eliminar el producto #$id."];
}
header("Location: index.php");
exit;
