<?php
require_once __DIR__ . '/../includes/security.php';
initSecureSession();
require_once '../includes/supabase_api.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_producto'])) {
    header("Location: ../index.php");
    exit;
}
$id_producto = (int)$_POST['id_producto'];
$respuesta = consultaSupabase("productos?id=eq.$id_producto&select=id,nombre,precio,stock");
$producto = (!empty($respuesta) && !isset($respuesta['error'])) ? $respuesta[0] : null;
if ($producto && $producto['stock'] >= 1) {
    $_SESSION['carrito'] ??= [];
    if (isset($_SESSION['carrito'][$id_producto])) {
        if ($_SESSION['carrito'][$id_producto]['cantidad'] + 1 <= $producto['stock']) {
            $_SESSION['carrito'][$id_producto]['cantidad']++;
        }
    } else {
        $_SESSION['carrito'][$id_producto] = [
            'nombre'   => $producto['nombre'],
            'precio'   => $producto['precio'],
            'cantidad' => 1
        ];
    }
}
header("Location: ../index.php?agregado=1");
exit;