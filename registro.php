<?php
require 'supabase_api.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass_plana = $_POST['password'] ?? '';
    
    $pass_encriptada = password_hash($pass_plana, PASSWORD_BCRYPT);

    $datos_usuario = [
        'nombre'   => $nombre,
        'apellido' => $apellido,
        'email'    => $email,
        'password' => $pass_encriptada,
        'rol_id'   => 2
    ];

    $respuesta = consultaSupabase('perfiles', 'POST', $datos_usuario);

    if (isset($respuesta['error']) || isset($respuesta['code'])) {
        if (isset($respuesta['code']) && $respuesta['code'] === '23505') {
            $error = "Este email ya está registrado.";
        } else {
            $mensaje = $respuesta['message'] ?? ($respuesta['error'] ?? 'Error desconocido');
            $error = "Error al guardar: " . $mensaje;
        }
    } else {
        header("Location: login.php?success=1");
        exit;
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
    <div class="container" style="max-width: 500px;">
        <div class="card shadow border-0 p-4">
            <h2 class="text-center mb-4 text-primary">Crear Cuenta</h2>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" name="apellido" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold">Registrarse</button>
            </form>
            <p class="text-center mt-3 small">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
        </div>
    </div>
</body>
</html>