<?php
$supabaseUrl = 'https://tckqheerzbjjpbfavwxn.supabase.co';
$supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InRja3FoZWVyemJqanBiZmF2d3huIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzMxMzcwODAsImV4cCI6MjA4ODcxMzA4MH0.W7ghQL-sNgCDlXli9oXozL9FY66iZh-mMTZtaFrLqvU';
function consultaSupabase($endpoint, $metodo = 'GET', $body = null) {
    global $supabaseUrl, $supabaseKey;
    $url = $supabaseUrl . "/rest/v1/" . $endpoint;
    $ch = curl_init($url);
    $headers = [
        "apikey: " . $supabaseKey,
        "Authorization: Bearer " . $supabaseKey,
        "Content-Type: application/json",
        "Prefer: return=representation"
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $metodo = strtoupper($metodo);
    if ($metodo !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
    }
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    if ($error) return ["error" => $error];
    $decoded = json_decode($response, true);
    if ($httpCode >= 400) {
        if (!is_array($decoded)) {
            $decoded = ["error" => "Error HTTP", "message" => $response];
        }
        $decoded['http_code'] = $httpCode;
    }
    return $decoded;
}
function subirImagenSupabase($file_input_name, $bucket = 'imagenes_productos') {
    global $supabaseUrl, $supabaseKey;
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $file = $_FILES[$file_input_name];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nombre_unico = uniqid('prod_', true) . '.' . $ext;
    $url = $supabaseUrl . "/storage/v1/object/" . $bucket . "/" . $nombre_unico;
    $ch = curl_init($url);
    $headers = [
        "apikey: " . $supabaseKey,
        "Authorization: Bearer " . $supabaseKey,
        "Content-Type: " . $file['type']
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file['tmp_name']));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode === 200) {
        return $supabaseUrl . "/storage/v1/object/public/" . $bucket . "/" . $nombre_unico;
    }
    return null;
}