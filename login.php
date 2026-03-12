<?php
session_start();
require 'includes/db.php';  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        
        $sql = "SELECT p.*, r.nombre as nombre_rol 
                FROM perfiles p 
                INNER JOIN roles r ON p.rol_id = r.id 
                WHERE p.email = :email LIMIT 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nombre'];
            
            
            $_SESSION['es_admin'] = ($user['nombre_rol'] === 'admin');

            header("Location: index.php");
            exit;
        } else {
            $error = "Email o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión | RecambiosPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container" style="max-width: 400px;">
        <div class="card shadow border-0 p-4 text-center">
            <h2 class="mb-4 text-primary fw-bold"><i class="bi bi-person-lock"></i> Acceso</h2>
            
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success small">¡Registro completado! Ya puedes entrar.</div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger small"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="nombre@ejemplo.com" required>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold">Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Entrar</button>
            </form>
            
            <div class="mt-3">
                <span class="small text-muted">¿No tienes cuenta?</span> 
                <a href="registro.php" class="small text-decoration-none">Regístrate aquí</a>
            </div>
        </div>
    </div>
</body>
</html>