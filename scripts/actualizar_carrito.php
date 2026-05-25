<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/supabase_api.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_producto']) || !isset($_POST['accion'])) {
    header("Location: ../carrito.php");
    exit;
}
$id_producto = (int)$_POST['id_producto'];
$accion = trim($_POST['accion']);
if (isset($_SESSION['carrito'][$id_producto])) {
    if ($accion === 'incrementar') {
        $respuesta = consultaSupabase("productos?id=eq.$id_producto&select=stock");
        $producto = (!empty($respuesta) && !isset($respuesta['error'])) ? $respuesta[0] : null;
        if ($producto) {
            if ($_SESSION['carrito'][$id_producto]['cantidad'] + 1 <= $producto['stock']) {
                $_SESSION['carrito'][$id_producto]['cantidad']++;
            }
        }
    } elseif ($accion === 'decrementar') {
        if ($_SESSION['carrito'][$id_producto]['cantidad'] - 1 > 0) {
            $_SESSION['carrito'][$id_producto]['cantidad']--;
        } else {
            unset($_SESSION['carrito'][$id_producto]);
        }
    } elseif ($accion === 'eliminar') {
        unset($_SESSION['carrito'][$id_producto]);
    }
}
header("Location: ../carrito.php");
exit;
