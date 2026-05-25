<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/supabase_api.php';
require_once 'includes/mailer.php';
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
$pendiente_verificacion = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre']   ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email    = trim(strtolower($_POST['email'] ?? ''));
    $pass_plana = $_POST['password'] ?? '';
    if (empty($nombre) || empty($apellido) || empty($email) || empty($pass_plana)) {
        $error = "Por favor, completa todos los campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del email no es válido.";
    } elseif (strlen($pass_plana) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres.";
    } else {
        $existe = consultaSupabase("perfiles?email=eq." . urlencode($email) . "&select=id");
        if (!empty($existe) && !isset($existe['error'])) {
            $error = "Este email ya está registrado. ¿Quieres <a href='login.php'>iniciar sesión</a>?";
        } else {
            $file_verif = __DIR__ . '/data/pending_verifications.json';
            $pendientes = file_exists($file_verif) ? (json_decode(file_get_contents($file_verif), true) ?: []) : [];
            $ya_pendiente = array_filter($pendientes, fn($p) => $p['email'] === $email && strtotime($p['expires_at']) > time());
            if (!empty($ya_pendiente)) {
                $error = "Ya tienes un registro pendiente de verificar. Revisa tu email (o espera 24h para volver a intentarlo).";
            } else {
                $pendientes = array_values(array_filter($pendientes, fn($p) => $p['email'] !== $email));
                $token   = bin2hex(random_bytes(32));
                $expiry  = date('Y-m-d H:i:s', strtotime('+24 hours'));
                $pendientes[] = [
                    'token'     => $token,
                    'nombre'    => $nombre,
                    'apellido'  => $apellido,
                    'email'     => $email,
                    'password'  => password_hash($pass_plana, PASSWORD_BCRYPT),
                    'rol_id'    => 2,
                    'expires_at' => $expiry,
                ];
                file_put_contents($file_verif, json_encode($pendientes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $link   = SITE_URL . '/verificar_email.php?token=' . $token;
                $cuerpo = emailPlantillaVerificacion(htmlspecialchars($nombre), $link);
                enviarEmail($email, 'Confirma tu cuenta en AutoStock', $cuerpo);
                $pendiente_verificacion = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | AutoStock</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --azul-oscuro: #192C76; --naranja: #FF7403; }
        .btn-naranja { background-color: var(--naranja); border-color: var(--naranja); color: #fff; }
        .btn-naranja:hover { background-color: #e66800; color: #fff; }
        .titulo-oscuro { color: var(--azul-oscuro); }
        body { background: #f4f6fb; }
        .strength-bar { height: 4px; border-radius: 2px; transition: width .3s, background .3s; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="container" style="max-width: 520px;">
        <div class="text-center mb-4">
            <a href="index.php">
                <img src="assets/img/logo.svg" alt="AutoStock" style="height:64px;width:auto;">
            </a>
        </div>
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <?php if ($pendiente_verificacion): ?>
                <div class="text-center py-2">
                    <div class="mb-3" style="font-size:3.5rem;">📬</div>
                    <h4 class="fw-bold titulo-oscuro">¡Casi listo!</h4>
                    <p class="text-muted">Hemos enviado un email de confirmación a <strong><?= htmlspecialchars($_POST['email'] ?? '') ?></strong>.<br>
                    Haz clic en el enlace del correo para activar tu cuenta.</p>
                    <div class="alert alert-info small text-start mt-3">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Si no ves el email, revisa tu carpeta de <strong>Spam</strong>. El enlace es válido durante <strong>24 horas</strong>.
                    </div>
                    <a href="login.php" class="btn btn-naranja rounded-pill px-4 mt-2 fw-bold">Ir al inicio de sesión</a>
                </div>
            <?php else: ?>
                <h4 class="fw-bold titulo-oscuro mb-1">Crear cuenta</h4>
                <p class="text-muted small mb-4">Regístrate para comprar, guardar tu vehículo y gestionar tus pedidos.</p>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger small py-2"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold small">Nombre</label>
                            <input type="text" name="nombre" class="form-control"
                                   value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold small">Apellido</label>
                            <input type="text" name="apellido" class="form-control"
                                   value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control border-start-0 ps-0"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" id="password"
                                   class="form-control border-start-0 ps-0"
                                   placeholder="Mínimo 8 caracteres" required minlength="8">
                            <button type="button" class="btn btn-outline-secondary" id="togglePass">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <div class="mt-2 bg-light rounded overflow-hidden" style="height:4px;">
                            <div class="strength-bar" id="strength-bar" style="width:0%;"></div>
                        </div>
                        <small id="strength-text" class="text-muted"></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Confirmar contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                            <input type="password" name="confirmar" id="confirmar"
                                   class="form-control border-start-0 ps-0"
                                   placeholder="Repite la contraseña" required>
                        </div>
                        <small id="match-msg" class="mt-1 d-none"></small>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="acepta" required>
                        <label class="form-check-label small" for="acepta">
                            Acepto la <a href="politica-privacidad.php" target="_blank" style="color:var(--azul-oscuro);">Política de Privacidad</a>
                            y el <a href="aviso-legal.php" target="_blank" style="color:var(--azul-oscuro);">Aviso Legal</a>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-naranja w-100 fw-bold rounded-pill py-2">
                        <i class="bi bi-person-plus me-2"></i> Crear cuenta
                    </button>
                </form>
                <p class="text-center mt-3 small text-muted">¿Ya tienes cuenta?
                    <a href="login.php" style="color:var(--azul-oscuro);">Inicia sesión</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
    <script>
        document.getElementById('togglePass')?.addEventListener('click', function() {
            const inp = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            inp.type = inp.type === 'password' ? 'text' : 'password';
            icon.className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
        });
        document.getElementById('password')?.addEventListener('input', function() {
            const val = this.value;
            const bar = document.getElementById('strength-bar');
            const txt = document.getElementById('strength-text');
            let score = 0;
            if (val.length >= 8)  score++;
            if (val.length >= 12) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            const configs = [
                { w: '20%', c: '#e74c3c', t: 'Muy débil' },
                { w: '40%', c: '#e67e22', t: 'Débil' },
                { w: '60%', c: '#f1c40f', t: 'Moderada' },
                { w: '80%', c: '#2ecc71', t: 'Fuerte' },
                { w: '100%', c: '#27ae60', t: 'Muy fuerte' },
            ];
            const cfg = val ? (configs[Math.min(score - 1, 4)] || configs[0]) : { w: '0%', c: '#e74c3c', t: '' };
            bar.style.width = cfg.w;
            bar.style.background = cfg.c;
            txt.textContent = val ? cfg.t : '';
            txt.style.color = cfg.c;
        });
        document.getElementById('confirmar')?.addEventListener('input', function() {
            const pass = document.getElementById('password').value;
            const msg  = document.getElementById('match-msg');
            msg.classList.remove('d-none');
            if (this.value === pass && this.value.length > 0) {
                msg.textContent = '✓ Las contraseñas coinciden';
                msg.className = 'mt-1 text-success small';
            } else {
                msg.textContent = '✗ Las contraseñas no coinciden';
                msg.className = 'mt-1 text-danger small';
            }
        });
    </script>
</body>
</html>
