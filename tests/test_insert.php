<?php
require 'supabase_api.php';
$relaciones = [
    ['producto_id' => 1, 'coche_id' => 1]
];
$res = consultaSupabase("coche_producto", "POST", $relaciones);
print_r($res);
