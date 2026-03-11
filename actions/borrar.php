<?php
require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Preparar delete con la tabla y columna correctas
    $sql = "DELETE FROM productos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
}

// Devolver al usuario al índice (catálogo principal)
header("Location: index.php");
exit;
?>