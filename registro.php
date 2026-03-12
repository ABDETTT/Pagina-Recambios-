<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'includes/db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass_plana = $_POST['password'] ?? '';
    
    $pass_encriptada = password_hash($pass_plana, PASSWORD_BCRYPT);

    $rol_cliente = 2;

    try {
        
        $sql = "INSERT INTO perfiles (id, nombre, apellido, email, password, rol_id) 
                VALUES (gen_random_uuid(), :nom, :ape, :email, :pass, :rol)";
        
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            'nom'   => $nombre,
            'ape'   => $apellido,
            'email' => $email,
            'pass'  => $pass_encriptada,
            'rol'   => $rol_cliente
        ]);

        
        header("Location: login.php?success=1");
        exit;

    } catch (PDOException $e) {
        
        if ($e->getCode() == 23505) {
            $error = "Este email ya está registrado.";
        } else {
            $error = "Error en la base de datos: " . $e->getMessage();
        }
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