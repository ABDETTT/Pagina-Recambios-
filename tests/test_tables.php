<?php
require 'supabase_api.php';
$pedidos = consultaSupabase("pedidos?select=*,detalle_pedido(*)&limit=1");
$garaje = consultaSupabase("garaje_usuario?select=*&limit=1");
$coches = consultaSupabase("coches?select=*&limit=1");
print_r(['pedidos' => $pedidos, 'garaje' => $garaje, 'coches' => $coches]);
