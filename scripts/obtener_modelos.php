<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/supabase_api.php';
if (isset($_GET['marca'])) {
    $marca = rawurlencode($_GET['marca']);
    $endpoint = "coches?select=modelo&marca=eq.$marca&order=modelo.asc";
    $respuesta = consultaSupabase($endpoint);
    header('Content-Type: application/json');
    echo json_encode($respuesta);
    exit;
}