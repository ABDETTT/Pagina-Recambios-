<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_producto'])) {
    $id_producto = (int)$_POST['id_producto'];
    $cantidad = 1; // Por defecto añadimos de 1 en 1

    // Verificamos que el producto existe y tiene stock
    $stmt = $pdo->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id = ?");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch();

    if ($producto && $producto['stock'] >= $cantidad) {
        // Si no existe el carrito en la sesión, lo creamos vacío
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // Si el producto ya está en el carrito, sumamos 1 a la cantidad
        if (isset($_SESSION['carrito'][$id_producto])) {
            // Comprobamos que no supere el stock real
            if ($_SESSION['carrito'][$id_producto]['cantidad'] + 1 <= $producto['stock']) {
                $_SESSION['carrito'][$id_producto]['cantidad'] += 1;
            }
        } else {
            // Si es la primera vez que añade este producto, lo metemos al carrito
            $_SESSION['carrito'][$id_producto] = [
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'cantidad' => $cantidad
            ];
        }
    }
    // Devolvemos al usuario a la tienda
    header("Location: index.php?agregado=1");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>