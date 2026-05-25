<?php
require 'supabase_api.php';
function listBuckets() {
    global $supabaseUrl, $supabaseKey;
    $url = $supabaseUrl . "/storage/v1/bucket";
    $ch = curl_init($url);
    $headers = [
        "apikey: " . $supabaseKey,
        "Authorization: Bearer " . $supabaseKey
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}
print_r(listBuckets());
