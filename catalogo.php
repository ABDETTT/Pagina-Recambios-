<?php
require 'db.php';

// --- LÓGICA DE PAGINACIÓN ---
$registros_por_pagina = 12;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;

$offset = ($pagina_actual - 1) * $registros_por_pagina;

// --- CONSULTA SQL ---
// Traemos productos y el nombre de su categoría adaptado al esquema real
$sql = "SELECT p.*, c.nombre as nombre_categoria 
        FROM productos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        ORDER BY p.id DESC 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $registros_por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll();
?>