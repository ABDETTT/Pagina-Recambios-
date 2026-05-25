<?php
require 'supabase_api.php';
$res = consultaSupabase("productos?limit=1");
print_r($res);
