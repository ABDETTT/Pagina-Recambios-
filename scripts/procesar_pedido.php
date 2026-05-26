<?php
require_once __DIR__ . '/../includes/security.php';
initSecureSession();
require_once '../includes/supabase_api.php';
require_once '../includes/mailer.php';
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
    header("Location: ../index.php");
    exit;
}
if (!csrfCheck()) {
    header("Location: ../carrito.php?error=csrf");
    exit;
}
$pagos_permitidos = ['Tarjeta de Crédito', 'Transferencia Bancaria', 'PayPal'];
$forma_pago = $_POST['forma_pago'] ?? '';
if (!in_array($forma_pago, $pagos_permitidos, true)) {
    $forma_pago = 'Tarjeta de Crédito';
}
$usuario_id = $_SESSION['usuario_id'];
$carrito = $_SESSION['carrito'];
$total = 0;
foreach ($carrito as $item) {
    $total += $item['precio'] * $item['cantidad'];
}
$datosPedido = [
    'usuario_id' => $usuario_id,
    'total'      => $total,
    'forma_pago' => $forma_pago,
    'estado'     => 'Pendiente',
    'fecha'      => date('Y-m-d H:i:s')
];
$resPedido = consultaSupabase("pedidos", "POST", [$datosPedido]);
if (isset($resPedido['error']) || !isset($resPedido[0]['id'])) {
    $_SESSION['error_compra'] = "Ocurrió un problema de conexión al procesar el pago. Por favor, inténtalo de nuevo.";
    header("Location: ../carrito.php?error=procesamiento_fallido");
    exit;
}
$pedido_id = $resPedido[0]['id'];
foreach ($carrito as $producto_id => $item) {
    $subtotal = $item['precio'] * $item['cantidad'];
    $datosDetalle = [
        'pedido_id'       => $pedido_id,
        'producto_id'     => $producto_id,
        'cantidad'        => $item['cantidad'],
        'precio_unitario' => $item['precio'],
        'subtotal'        => $subtotal
    ];
    consultaSupabase("detalle_pedido", "POST", [$datosDetalle]);
    $resProd = consultaSupabase("productos?id=eq.$producto_id&select=stock");
    if (!empty($resProd) && !isset($resProd['error'])) {
        $nuevoStock = $resProd[0]['stock'] - $item['cantidad'];
        consultaSupabase("productos?id=eq.$producto_id", "PATCH", ['stock' => max(0, $nuevoStock)]);
    }
}
$email_cliente = $_SESSION['email'] ?? '';
$nombre_cliente = $_SESSION['nombre'] ?? 'Cliente';
if ($email_cliente) {
    $items_email = [];
    foreach ($carrito as $item) {
        $items_email[] = [
            'nombre'   => $item['nombre'],
            'cantidad' => $item['cantidad'],
            'precio'   => $item['precio'],
        ];
    }
    
    $destinatario = $_POST['destinatario'] ?? '';
    $calle = $_POST['calle'] ?? '';
    $cp = $_POST['codigo_postal'] ?? '';
    $ciudad = $_POST['ciudad'] ?? '';
    $provincia = $_POST['provincia'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    
    $direccion_completa = trim("$destinatario, $calle, $cp $ciudad ($provincia) - Tel: $telefono", ", -");
    if ($direccion_completa === "Tel:") $direccion_completa = "";

    $cuerpo_email = emailPlantillaConfirmacionPedido(
        htmlspecialchars($nombre_cliente),
        $pedido_id,
        $total,
        $items_email,
        $forma_pago,
        $direccion_completa
    );
    enviarEmail($email_cliente, "Confirmación de pedido #$pedido_id — AutoStock", $cuerpo_email);
}
unset($_SESSION['carrito']);
header("Location: ../panel_cliente.php?pedido_exito=1");
exit;
