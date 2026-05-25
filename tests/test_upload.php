<?php
require 'supabase_api.php';
function subirImagenTest() {
    global $supabase_url, $supabase_key;
    $temp_file = tempnam(sys_get_temp_dir(), 'img');
    file_put_contents($temp_file, 'fake image content');
    $bucket = 'imagenes_productos';
    $file_name = 'test_' . time() . '.txt';
    $url = "$supabase_url/storage/v1/object/$bucket/$file_name";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    $headers = [
        "apikey: $supabase_key",
        "Authorization: Bearer $supabase_key",
        "Content-Type: text/plain"
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $file_contents = file_get_contents($temp_file);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $file_contents);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    unlink($temp_file);
    return ['code' => $httpCode, 'response' => $response];
}
$res = subirImagenTest();
print_r($res);
