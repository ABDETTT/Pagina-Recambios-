<?php
session_start();
require '../includes/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_producto'])) {
    header("Location: /AutoStock/index.php");
    exit;
}
$id_producto = (int)$_POST['id_producto'];
$stmt = $pdo->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id = ?");
$stmt->execute([$id_producto]);
$producto = $stmt->fetch();
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
header("Location: /AutoStock/index.php?agregado=1");
exit;