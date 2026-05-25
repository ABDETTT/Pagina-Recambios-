<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/supabase_api.php';
$token  = trim($_GET['token'] ?? '');
$estado = 'error'; 
if ($token) {
    $file = __DIR__ . '/data/pending_verifications.json';
    if (file_exists($file)) {
        $pendientes = json_decode(file_get_contents($file), true) ?: [];
        $encontrado = null;
        foreach ($pendientes as $p) {
            if ($p['token'] === $token) {
                $encontrado = $p;
                break;
            }
        }
        if ($encontrado) {
            if (strtotime($encontrado['expires_at']) < time()) {
                $estado = 'expirado';
                $pendientes = array_values(array_filter($pendientes, fn($p) => $p['token'] !== $token));
                file_put_contents($file, json_encode($pendientes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } else {
                $existe = consultaSupabase("perfiles?email=eq." . urlencode($encontrado['email']) . "&select=id");
                if (!empty($existe) && !isset($existe['error'])) {
                    $estado = 'ya_existe';
                } else {
                    $datos = [
                        'nombre'   => $encontrado['nombre'],
                        'apellido' => $encontrado['apellido'],
                        'email'    => $encontrado['email'],
                        'password' => $encontrado['password'],
                        'rol_id'   => $encontrado['rol_id'],
                    ];
                    $res = consultaSupabase('perfiles', 'POST', $datos);
                    if (!isset($res['error']) && !isset($res['http_code'])) {
                        $estado = 'ok';
                        $nombre_usuario = $encontrado['nombre'];
                        $pendientes = array_values(array_filter($pendientes, fn($p) => $p['token'] !== $token));
                        file_put_contents($file, json_encode($pendientes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificar Email | AutoStock</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --azul-oscuro: #192C76; --naranja: #FF7403; }
        .btn-naranja { background-color: var(--naranja); border-color: var(--naranja); color: #fff; }
        .btn-naranja:hover { background-color: #e66800; color: #fff; }
        .titulo-oscuro { color: var(--azul-oscuro); }
        body { background: #f4f6fb; }
    </style>
    <?php if ($estado === 'ok'): ?>
    <meta http-equiv="refresh" content="5;url=login.php?success=1">
    <?php endif; ?>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="container" style="max-width: 480px;">
        <div class="text-center mb-4">
            <a href="index.php">
                <img src="assets/img/logo.svg" alt="AutoStock" style="height:64px;width:auto;">
            </a>
        </div>
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
            <?php if ($estado === 'ok'): ?>
                <div style="font-size:4rem;" class="mb-3">✅</div>
                <h4 class="fw-bold titulo-oscuro">¡Cuenta verificada!</h4>
                <p class="text-muted">Bienvenido/a a AutoStock, <strong><?= htmlspecialchars($nombre_usuario ?? '') ?></strong>.
                Tu cuenta está activa. Serás redirigido en 5 segundos…</p>
                <a href="login.php?success=1" class="btn btn-naranja rounded-pill px-4 fw-bold mt-2">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión ahora
                </a>
            <?php elseif ($estado === 'expirado'): ?>
                <div style="font-size:4rem;" class="mb-3">⏰</div>
                <h4 class="fw-bold titulo-oscuro">Enlace expirado</h4>
                <p class="text-muted">El enlace de verificación ha caducado (válido 24h). Necesitas registrarte de nuevo.</p>
                <a href="registro.php" class="btn btn-naranja rounded-pill px-4 fw-bold mt-2">Registrarse de nuevo</a>
            <?php elseif ($estado === 'ya_existe'): ?>
                <div style="font-size:4rem;" class="mb-3">ℹ️</div>
                <h4 class="fw-bold titulo-oscuro">Email ya registrado</h4>
                <p class="text-muted">Esta cuenta ya fue activada anteriormente. Puedes iniciar sesión directamente.</p>
                <a href="login.php" class="btn btn-naranja rounded-pill px-4 fw-bold mt-2">Iniciar sesión</a>
            <?php else: ?>
                <div style="font-size:4rem;" class="mb-3">❌</div>
                <h4 class="fw-bold titulo-oscuro">Enlace no válido</h4>
                <p class="text-muted">El enlace de verificación no es válido o ya fue utilizado.</p>
                <div class="d-flex gap-2 justify-content-center mt-3">
                    <a href="registro.php" class="btn btn-outline-secondary rounded-pill px-3">Registrarse</a>
                    <a href="login.php" class="btn btn-naranja rounded-pill px-3 fw-bold">Iniciar sesión</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
