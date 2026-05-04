<?php
session_start();
require 'supabase_api.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    $rol_id = 2;

    $nuevoUsuario = [
        'nombre'   => $nombre,
        'apellido' => $apellido,
        'email'    => $email,
        'password' => $password,
        'rol_id'   => $rol_id
    ];

    $url = $supabaseUrl . "/rest/v1/perfiles";
    $ch = curl_init($url);
    $headers = [
        "apikey: " . $supabaseKey,
        "Authorization: Bearer " . $supabaseKey,
        "Content-Type: application/json",
        "Prefer: return=representation"
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($nuevoUsuario));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        header("Location: login.php?success=1");
        exit;
    } else {
        $error = "Error al registrar el usuario. Inténtalo de nuevo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | RecambiosPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container" style="max-width: 450px;">
        <div class="card shadow border-0 p-4">
            <h2 class="text-center mb-4 fw-bold text-primary">Crear Cuenta</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger small"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Apellido</label>
                        <input type="text" name="apellido" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold">Registrarse</button>
            </form>
            <div class="text-center mt-3">
                <a href="login.php" class="small text-decoration-none">¿Ya tienes cuenta? Inicia sesión</a>
            </div>
        </div>
    </div>
</body>
</html>