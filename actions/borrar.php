<?php
require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    
    $sql = "DELETE FROM productos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;
?>