<?php
require_once __DIR__ . '/../includes/security.php';
initSecureSession();
require_once '../includes/supabase_api.php';
if (isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfCheck()) {
        $error = "Solicitud no válida. Recarga la página e inténtalo de nuevo.";
    } elseif (!rateLimit('login', 5, 300)) {
        $error = "Demasiados intentos. Espera unos minutos antes de volver a intentarlo.";
    } else {
    $email = trim($_POST['email'] ?? '');
    $password_ingresada = $_POST['password'] ?? '';
    if (empty($email) || empty($password_ingresada)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        $endpoint = "perfiles?email=eq." . urlencode($email) . "&select=*";
        $respuesta = consultaSupabase($endpoint);
        if (!empty($respuesta) && !isset($respuesta['error'])) {
            $usuario = $respuesta[0];
            if (password_verify($password_ingresada, $usuario['password'])) {
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['apellido'] = $usuario['apellido'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['es_admin'] = ($usuario['rol_id'] == 1);
                header("Location: ../index.php");
                exit;
            } else {
                $error = "Credenciales incorrectas.";
            }
        } else {
            $error = "Credenciales incorrectas.";
        }
    }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | AutoStock</title>
    <link rel="icon" type="image/svg+xml" href="../assets/img/logo.svg">
    <link href="../assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1" defer></script>
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container" style="max-width: 400px;">
        <div class="text-center mb-3">
            <a href="../index.php">
                <img src="../assets/img/logo.svg" alt="AutoStock" style="height:64px;width:auto;">
            </a>
        </div>
        <div class="card shadow border-0 p-4">
            <h2 class="text-center mb-4 fw-bold titulo-oscuro">Iniciar Sesión</h2>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success small">¡Registro completado! Ya puedes iniciar sesión.</div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger small"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST" action="login.php">
                <?= csrfField() ?>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-1">
                    <label class="form-label small fw-bold">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="text-end mb-4">
                    <a href="recuperar_password.php" class="small text-decoration-none" style="color:var(--naranja,#FF7403);">¿Olvidaste tu contraseña?</a>
                </div>
                <button type="submit" class="btn btn-naranja w-100 fw-bold">Entrar</button>
            </form>
            <div class="text-center mt-3">
                <a href="registro.php" class="small text-decoration-none">¿No tienes cuenta? Regístrate aquí</a>
            </div>
        </div>
    </div>
</body>
</html>