<?php
define('MAIL_FROM',      'noreply@autostock.com');
define('MAIL_FROM_NAME', 'AutoStock');
define('MAIL_REPLY_TO',  'soporte@autostock.com');
define('SITE_URL',       'http://localhost:8000');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
function enviarEmail(string $para, string $asunto, string $cuerpo_html): bool {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'TU_CORREO@gmail.com'; // Sustituye por tu correo de Gmail
        $mail->Password = 'TU_CONTRASEÑA_DE_APLICACION'; // Sustituye por tu contraseña de aplicación de 16 dígitos
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($para);
        $mail->addReplyTo(MAIL_REPLY_TO, 'Soporte AutoStock');
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpo_html;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
function emailLayout(string $contenido): string {
    $year = date('Y');
    $site_url = SITE_URL;
    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  body{margin:0;padding:0;background:#f4f6fb;font-family:Arial,Helvetica,sans-serif;}
  .wrapper{max-width:600px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(25,44,118,.12);}
  .top-bar{background:#FF7403;height:6px;}
  .header{background:#192C76;padding:28px 32px;text-align:center;}
  .header h1{color:#fff;margin:0;font-size:26px;letter-spacing:-0.5px;font-weight:900;}
  .header h1 span{color:#93DAFE;}
  .body{padding:40px 32px;color:#333;line-height:1.75;font-size:15px;}
  .body h2{color:#192C76;margin-top:0;font-size:22px;}
  .btn{display:inline-block;background:#FF7403;color:#fff !important;text-decoration:none;padding:14px 36px;border-radius:50px;font-weight:700;font-size:15px;margin:22px 0;letter-spacing:0.3px;}
  .info-box{background:#f0f4ff;border-left:4px solid #192C76;border-radius:6px;padding:16px 20px;margin:24px 0;color:#192C76;}
  .success-icon{font-size:52px;text-align:center;display:block;margin-bottom:16px;}
  .divider{border:none;border-top:1px solid #e8ecf4;margin:28px 0;}
  .footer{background:#f4f6fb;padding:22px 32px;text-align:center;color:#999;font-size:12px;border-top:1px solid #e8ecf4;}
  .footer a{color:#192C76;text-decoration:none;}
  table.order-items{width:100%;border-collapse:collapse;margin:16px 0;}
  table.order-items th{background:#192C76;color:#fff;padding:10px 14px;text-align:left;font-size:13px;}
  table.order-items td{padding:10px 14px;border-bottom:1px solid #e8ecf4;font-size:14px;}
  table.order-items tr:last-child td{border-bottom:none;}
  .total-row td{font-weight:700;color:#192C76;background:#f0f4ff;}
  .status-badge{display:inline-block;padding:6px 16px;border-radius:20px;font-weight:700;font-size:13px;}
</style>
</head>
<body>
<div class="wrapper">
  <div class="top-bar"></div>
  <div class="header">
    <h1>Auto<span>Stock</span></h1>
  </div>
  <div class="body">
    $contenido
  </div>
  <div class="footer">
    &copy; $year AutoStock &mdash; Tu tienda de confianza para repuestos de automóvil<br>
    <a href="$site_url">autostock.com</a> &bull; <a href="$site_url/contacto.php">Contacto</a><br>
    <small style="color:#bbb;">Si no has solicitado este correo, simplemente ignóralo.</small>
  </div>
</div>
</body>
</html>
HTML;
}
function emailPlantillaVerificacion(string $nombre, string $link): string {
    $contenido = <<<HTML
    <span class="success-icon">✉️</span>
    <h2>¡Bienvenido/a a AutoStock, $nombre!</h2>
    <p>Gracias por registrarte. Solo falta un paso: <strong>confirma tu dirección de email</strong> haciendo clic en el botón de abajo.</p>
    <div class="info-box">
      ⏱️ Este enlace de verificación es válido durante <strong>24 horas</strong>.
    </div>
    <p style="text-align:center;">
      <a href="$link" class="btn">✅ Verificar mi Email</a>
    </p>
    <hr class="divider">
    <p style="color:#888;font-size:13px;">Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
    <a href="$link" style="color:#192C76;word-break:break-all;">$link</a></p>
HTML;
    return emailLayout($contenido);
}
function emailPlantillaRecuperarPassword(string $nombre, string $link): string {
    $contenido = <<<HTML
    <span class="success-icon">🔑</span>
    <h2>Restablecer contraseña</h2>
    <p>Hola <strong>$nombre</strong>, hemos recibido una solicitud para restablecer la contraseña de tu cuenta.</p>
    <div class="info-box">
      ⚠️ Si no has solicitado este cambio, ignora este correo. Tu contraseña no cambiará.
    </div>
    <p>Haz clic en el botón para crear una nueva contraseña:</p>
    <p style="text-align:center;">
      <a href="$link" class="btn">🔐 Restablecer Contraseña</a>
    </p>
    <p style="color:#888;font-size:13px;margin-top:20px;">⏱️ Este enlace expira en <strong>1 hora</strong>.<br>
    Si el botón no funciona, copia y pega este enlace:<br>
    <a href="$link" style="color:#192C76;word-break:break-all;">$link</a></p>
HTML;
    return emailLayout($contenido);
}
function emailPlantillaConfirmacionPedido(string $nombre, int $pedido_id, float $total, array $items, string $forma_pago, string $direccion_envio = ''): string {
    $filas = '';
    foreach ($items as $item) {
        $subtotal = number_format($item['precio'] * $item['cantidad'], 2, ',', '.');
        $precio_u = number_format($item['precio'], 2, ',', '.');
        $filas .= "<tr>
            <td>{$item['nombre']}</td>
            <td style='text-align:center;'>{$item['cantidad']}</td>
            <td style='text-align:right;'>{$precio_u} €</td>
            <td style='text-align:right;'>{$subtotal} €</td>
        </tr>";
    }
    $total_fmt  = number_format($total, 2, ',', '.');
    $base_fmt   = number_format($total / 1.21, 2, ',', '.');
    $iva_fmt    = number_format($total - ($total / 1.21), 2, ',', '.');
    $panel_url  = SITE_URL . '/panel_cliente.php';
    
    $direccion_html = $direccion_envio ? "<br>🏠 Dirección de envío: " . htmlspecialchars($direccion_envio) : "";

    $contenido = <<<HTML
    <span class="success-icon">🎉</span>
    <h2>¡Pedido confirmado!</h2>
    <p>Hola <strong>$nombre</strong>, hemos recibido tu pedido correctamente. Lo tendrás listo en breve.</p>
    <div class="info-box">
      📦 <strong>Número de pedido: #$pedido_id</strong><br>
      💳 Forma de pago: $forma_pago$direccion_html
    </div>
    <table class="order-items">
      <thead>
        <tr>
          <th>Producto</th>
          <th style="text-align:center;">Cant.</th>
          <th style="text-align:right;">Precio</th>
          <th style="text-align:right;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        $filas
        <tr class="total-row">
          <td colspan="3" style="text-align:right;">Base imponible:</td>
          <td style="text-align:right;">$base_fmt €</td>
        </tr>
        <tr class="total-row">
          <td colspan="3" style="text-align:right;">IVA (21%):</td>
          <td style="text-align:right;">$iva_fmt €</td>
        </tr>
        <tr class="total-row">
          <td colspan="3" style="text-align:right;font-size:16px;">TOTAL:</td>
          <td style="text-align:right;font-size:16px;color:#FF7403;">$total_fmt €</td>
        </tr>
      </tbody>
    <p style="text-align:center;margin-top:8px;">
      <a href="$panel_url" style="color:#192C76;">Ver todos mis pedidos</a>
    </p>
HTML;
    return emailLayout($contenido);
}
function emailPlantillaCambioEstado(string $nombre, string $email_dest, int $pedido_id, string $nuevo_estado): string {
    $iconos = [
        'Pendiente'      => '⏳',
        'En Preparación' => '🔧',
        'Enviado'        => '🚚',
        'Entregado'      => '✅',
        'Cancelado'      => '❌',
    ];
    $colores = [
        'Pendiente'      => '#f59e0b',
        'En Preparación' => '#3b82f6',
        'Enviado'        => '#8b5cf6',
        'Entregado'      => '#22c55e',
        'Cancelado'      => '#ef4444',
    ];
    $icono  = $iconos[$nuevo_estado]  ?? '📦';
    $color  = $colores[$nuevo_estado] ?? '#192C76';
    $panel_url = SITE_URL . '/panel_cliente.php';
    $mensaje_extra = '';
    if ($nuevo_estado === 'Enviado') {
        $mensaje_extra = '<p>Tu pedido está en camino. Recibirás tu paquete en los próximos 2-5 días hábiles.</p>';
    } elseif ($nuevo_estado === 'Entregado') {
        $mensaje_extra = '<p>¡Esperamos que todo esté perfecto! Si tienes algún problema, contáctanos sin dudarlo.</p>';
    } elseif ($nuevo_estado === 'Cancelado') {
        $mensaje_extra = '<p>Lamentamos cancelar tu pedido. Si crees que es un error o necesitas ayuda, <a href="' . SITE_URL . '/contacto.php" style="color:#192C76;">contáctanos</a>.</p>';
    }
    $contenido = <<<HTML
    <h2>Actualización de tu pedido #$pedido_id</h2>
    <p>Hola <strong>$nombre</strong>, te informamos que el estado de tu pedido ha cambiado:</p>
    <p style="text-align:center;">
      <span class="status-badge" style="background:$color;color:#fff;font-size:16px;">
        $icono $nuevo_estado
      </span>
    </p>
    $mensaje_extra
    <p style="text-align:center;margin-top:24px;">
      <a href="$panel_url" class="btn">Ver mis pedidos</a>
    </p>
HTML;
    return emailLayout($contenido);
}
