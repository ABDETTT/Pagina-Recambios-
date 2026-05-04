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

    if ($metodo === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) return ["error" => $error];

    return json_decode($response, true);
}