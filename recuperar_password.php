<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/supabase_api.php';
require_once 'includes/mailer.php';
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
$exito = false;
$error  = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(strtolower($_POST['email'] ?? ''));
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Introduce un email válido.";
    } else {
        $respuesta = consultaSupabase("perfiles?email=eq." . urlencode($email) . "&select=id,nombre");
        if (!empty($respuesta) && !isset($respuesta['error'])) {
            $usuario = $respuesta[0];
            $token   = bin2hex(random_bytes(32));
            $expiry  = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $file = __DIR__ . '/data/reset_tokens.json';
            $tokens = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
            $tokens = array_values(array_filter($tokens, fn($t) => $t['email'] !== $email));
            $tokens[] = ['token' => $token, 'email' => $email, 'expires_at' => $expiry];
            file_put_contents($file, json_encode($tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $link  = SITE_URL . '/resetear_password.php?token=' . $token;
            $cuerpo = emailPlantillaRecuperarPassword(htmlspecialchars($usuario['nombre']), $link);
            enviarEmail($email, 'Restablecer contraseña — AutoStock', $cuerpo);
        }
        $exito = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña | AutoStock</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --azul-oscuro: #192C76; --naranja: #FF7403; }
        .btn-naranja { background-color: var(--naranja); border-color: var(--naranja); color: #fff; }
        .btn-naranja:hover { background-color: #e66800; border-color: #e66800; color: #fff; }
        .titulo-oscuro { color: var(--azul-oscuro); }
        body { background: #f4f6fb; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="container" style="max-width: 440px;">
        <div class="text-center mb-4">
            <a href="index.php">
                <img src="assets/img/logo.svg" alt="AutoStock" style="height:64px;width:auto;">
            </a>
        </div>
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <?php if ($exito): ?>
                <div class="text-center py-2">
                    <div class="mb-3" style="font-size:3.5rem;">📬</div>
                    <h4 class="fw-bold titulo-oscuro">Revisa tu bandeja</h4>
                    <p class="text-muted">Si existe una cuenta con ese email, te hemos enviado un enlace para restablecer tu contraseña.<br>
                    El enlace expirará en <strong>1 hora</strong>.</p>
                    <a href="login.php" class="btn btn-naranja rounded-pill px-4 mt-2 fw-bold">Volver al inicio de sesión</a>
                </div>
            <?php else: ?>
                <div class="mb-4">
                    <a href="login.php" class="text-decoration-none text-muted small d-flex align-items-center gap-1 mb-3">
                        <i class="bi bi-arrow-left"></i> Volver al login
                    </a>
                    <h4 class="fw-bold titulo-oscuro mb-1">¿Olvidaste tu contraseña?</h4>
                    <p class="text-muted small mb-0">Introduce tu email y te enviaremos un enlace para crear una nueva.</p>
                </div>
                <?php if ($error): ?>
                    <div class="alert alert-danger small py-2"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control border-start-0 ps-0"
                                   placeholder="tu@email.com"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-naranja w-100 fw-bold rounded-pill py-2 mt-1">
                        <i class="bi bi-send me-2"></i> Enviar enlace de recuperación
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
