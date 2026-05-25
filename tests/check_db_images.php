<?php
require 'supabase_api.php';
$res = consultaSupabase("productos?select=id,nombre,imagen_url&order=id.desc&limit=5");
echo "<pre>";
print_r($res);
echo "</pre>";
