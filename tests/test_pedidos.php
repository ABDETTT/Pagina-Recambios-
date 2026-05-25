<?php
require 'supabase_api.php';
$res = consultaSupabase("pedidos?select=*&limit=1");
print_r($res);
