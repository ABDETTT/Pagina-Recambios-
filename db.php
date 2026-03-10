<?php
$host = 'aws-1-eu-west-1.pooler.supabase.com';
$port = '6543';
$db   = 'postgres';
$user = 'postgres.tckqheerzbjjpbfavwxn';
$pass = 'ABDEhajji2003';

// Asegúrate de que no haya espacios extra alrededor del signo igual
$dsn = "pgsql:host={$host};port={$port};dbname={$db}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
} catch (\PDOException $e) {
    // Esto te dará una pista más clara si el problema es de red o de sintaxis
    die("Error de conexión: " . $e->getMessage());
}
?>