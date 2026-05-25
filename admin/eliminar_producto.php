<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/supabase_api.php';
$es_admin = isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true;
if (!$es_admin) {
    header("Location: ../index.php");
    exit;
}
$id = $_GET['id'] ?? null;
if ($id) {
    $res = consultaSupabase("productos?id=eq.$id", "DELETE");
    if (!isset($res['error']) && !isset($res['http_code'])) {
        header("Location: index.php?eliminado=1");
    } else {
        header("Location: index.php?error=no_se_pudo_eliminar");
    }
    exit;
} else {
    header("Location: index.php");
    exit;
}