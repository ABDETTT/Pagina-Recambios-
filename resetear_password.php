<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/supabase_api.php';
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
$token      = trim($_GET['token'] ?? '');
$token_valido = false;
$token_data   = null;
$exito        = false;
$error        = '';
if ($token) {
    $file = __DIR__ . '/data/reset_tokens.json';
    if (file_exists($file)) {
        $tokens = json_decode(file_get_contents($file), true) ?: [];
        foreach ($tokens as $t) {
            if ($t['token'] === $token && strtotime($t['expires_at']) > time()) {
                $token_valido = true;
                $token_data   = $t;
                break;
            }
        }
    }
}
if (!$token || !$token_valido) {
    $error_pagina = "Este enlace no es válido o ha expirado. Solicita uno nuevo.";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_valido) {
    $nueva_pass  = $_POST['password']     ?? '';
    $confirmar   = $_POST['confirmar']    ?? '';
    if (strlen($nueva_pass) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($nueva_pass !== $confirmar) {
        $error = "Las contraseñas no coinciden.";
    } else {
        $hash = password_hash($nueva_pass, PASSWORD_BCRYPT);
        $res  = consultaSupabase(
            "perfiles?email=eq." . urlencode($token_data['email']),
            "PATCH",
            ['password' => $hash]
        );
        if (!isset($res['error'])) {
            $file   = __DIR__ . '/data/reset_tokens.json';
            $tokens = json_decode(file_get_contents($file), true) ?: [];
            $tokens = array_values(array_filter($tokens, fn($t) => $t['token'] !== $token));
            file_put_contents($file, json_encode($tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $exito = true;
        } else {
            $error = "Error al actualizar la contraseña. Inténtalo de nuevo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña | AutoStock</title>
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
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="container" style="max-width: 440px;">
        <div class="text-center mb-4">
            <a href="index.php">
                <img src="assets/img/logo.svg" alt="AutoStock" style="height:64px;width:auto;">
            </a>
        </div>
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <?php if (isset($error_pagina)): ?>
                <div class="text-center py-2">
                    <div class="mb-3" style="font-size:3.5rem;">⏰</div>
                    <h4 class="fw-bold titulo-oscuro">Enlace expirado</h4>
                    <p class="text-muted"><?= htmlspecialchars($error_pagina) ?></p>
                    <a href="recuperar_password.php" class="btn btn-naranja rounded-pill px-4 mt-2 fw-bold">Solicitar nuevo enlace</a>
                </div>
            <?php elseif ($exito): ?>
                <div class="text-center py-2">
                    <div class="mb-3" style="font-size:3.5rem;">🎉</div>
                    <h4 class="fw-bold titulo-oscuro">¡Contraseña actualizada!</h4>
                    <p class="text-muted">Tu contraseña ha sido cambiada correctamente. Ya puedes iniciar sesión.</p>
                    <a href="login.php" class="btn btn-naranja rounded-pill px-4 mt-2 fw-bold">Iniciar sesión</a>
                </div>
            <?php else: ?>
                <div class="mb-4">
                    <h4 class="fw-bold titulo-oscuro mb-1">Crea tu nueva contraseña</h4>
                    <p class="text-muted small mb-0">Elige una contraseña segura de al menos 8 caracteres.</p>
                </div>
                <?php if ($error): ?>
                    <div class="alert alert-danger small py-2"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST" id="form-reset">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nueva contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" id="password"
                                   class="form-control border-start-0 ps-0"
                                   placeholder="Mínimo 8 caracteres" required minlength="8">
                            <button type="button" class="btn btn-outline-secondary border-start-0" id="togglePass">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <div class="mt-2 bg-light rounded overflow-hidden" style="height:4px;">
                            <div class="strength-bar" id="strength-bar" style="width:0%;background:#e74c3c;"></div>
                        </div>
                        <small class="text-muted" id="strength-text"></small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Confirmar contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                            <input type="password" name="confirmar" id="confirmar"
                                   class="form-control border-start-0 ps-0"
                                   placeholder="Repite la contraseña" required>
                        </div>
                        <small id="match-msg" class="mt-1 d-none"></small>
                    </div>
                    <button type="submit" class="btn btn-naranja w-100 fw-bold rounded-pill py-2">
                        <i class="bi bi-check-circle me-2"></i> Guardar nueva contraseña
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <script>
        // Toggle visibilidad contraseña
        document.getElementById('togglePass')?.addEventListener('click', function() {
            const inp = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                inp.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
        // Indicador fortaleza contraseña
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
            const cfg = configs[Math.min(score - 1, 4)] || { w: '0%', c: '#e74c3c', t: '' };
            bar.style.width = val ? cfg.w : '0%';
            bar.style.background = cfg.c;
            txt.textContent = val ? cfg.t : '';
            txt.style.color = cfg.c;
        });
        // Verificar coincidencia
        document.getElementById('confirmar')?.addEventListener('input', function() {
            const pass = document.getElementById('password').value;
            const msg  = document.getElementById('match-msg');
            msg.classList.remove('d-none');
            if (this.value === pass) {
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
