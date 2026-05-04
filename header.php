<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$items_carrito = array_sum(array_column($_SESSION['carrito'] ?? [], 'cantidad'));
$titulo = $pagina_titulo ?? 'RecambiosPro | Tu tienda de repuestos';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $titulo; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  
  <link rel="stylesheet" href="assets/css/Styles/styles.css">
</head>
<body class="d-flex flex-column min-vh-100">

  <header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-azul-oscuro sticky-top shadow-sm py-3" style="border-bottom: 3px solid var(--naranja);">
      <div class="container align-items-center">
        
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center text-white" href="index.php">
          <i class="bi bi-gear-wide-connected text-naranja me-2"></i> Recambios<span class="text-azul-claro">Pro</span>
        </a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
          
          <ul class="navbar-nav ms-auto align-items-center gap-2 gap-lg-3">
            <li class="nav-item">
                <a class="nav-link nav-link-custom" href="index.php">Inicio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-link-custom" href="pages/catalogo.php">Catálogo</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-link-custom" href="pages/tutoriales.php">Tutoriales</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-link-custom" href="pages/contacto.php">Contacto</a>
            </li>
            
            <li class="nav-item d-none d-lg-block"><div class="vr bg-light opacity-25" style="height: 24px;"></div></li>
            
            <?php if (isset($_SESSION['usuario_nombre'])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle nav-link-custom d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5 me-1 text-azul-claro"></i> <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3">
                        <li><a class="dropdown-item py-2" href="#"><i class="bi bi-box-seam me-2 text-azul-oscuro"></i> Mis Pedidos</a></li>
                        <?php if (isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2" href="crear.php"><i class="bi bi-plus-circle me-2 text-naranja"></i> Añadir Producto</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger fw-bold" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Salir</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li class="nav-item d-flex gap-2 w-100 justify-content-center mt-2 mt-lg-0">
                    <a class="btn btn-outline-light btn-sm fw-bold rounded-pill px-3 py-2" href="login.php">Iniciar Sesión</a>
                    <a class="btn btn-naranja btn-sm fw-bold rounded-pill px-3 py-2" href="registro.php">Registrarse</a>
                </li>
            <?php endif; ?>

            <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                <a class="btn btn-naranja text-white fw-bold rounded-pill px-3 py-2 d-flex align-items-center position-relative shadow-sm" href="carrito.php">
                    <i class="bi bi-cart3 fs-5"></i>
                    <?php if($items_carrito > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-azul-oscuro border border-2 border-white" style="font-size: 0.75em;">
                            <?php echo $items_carrito; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>