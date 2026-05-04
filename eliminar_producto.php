<?php
session_start();
require 'supabase_api.php';

$es_admin = isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true;

if (!$es_admin) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $endpoint = "productos?id=eq." . $id;
    $url = $supabaseUrl . "/rest/v1/" . $endpoint;
    
    $ch = curl_init($url);
    $headers = [
        "apikey: " . $supabaseKey,
        "Authorization: Bearer " . $supabaseKey,
        "Content-Type: application/json"
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        header("Location: index.php?eliminado=1");
    } else {
        header("Location: index.php?error=no_se_pudo_eliminar");
    }
    exit;
} else {
    header("Location: index.php");
    exit;
}