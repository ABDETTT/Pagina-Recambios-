<?php
require 'supabase_api.php';
$datosActualizados = ['nombre' => 'Test'];
$res = consultaSupabase("productos?id=eq.1", "PATCH", $datosActualizados);
print_r($res);
