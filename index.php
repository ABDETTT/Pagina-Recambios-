<?php
// 0. INICIAMOS LA SESIÓN
session_start();

// 1. Incluimos la conexión a la base de datos
require 'db.php';

// 2. Lógica de Búsqueda y Paginación
$busqueda = isset($_GET['s']) ? trim($_GET['s']) : '';
$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina < 1) $pagina = 1;
$por_pagina = 8;
$offset = ($pagina - 1) * $por_pagina;

try {
    // 3. Consulta SQL (Busca en productos y trae el nombre de la categoría)
    if (!empty($busqueda)) {
        $sql = "SELECT p.*, c.nombre as cat_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.nombre ILIKE :query OR p.descripcion ILIKE :query OR c.nombre ILIKE :query
                ORDER BY p.id DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':query', '%' . $busqueda . '%');
    } else {
        $sql = "SELECT p.*, c.nombre as cat_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id
                ORDER BY p.id DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
    }

    $stmt->bindValue(':limit', (int)$por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    $productos = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RecambiosPro | Tienda Oficial</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  
  <style>
    .card-hover { transition: transform 0.3s ease; }
    .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .hero-section { 
        background: linear-gradient(rgba(0,36,107,0.8), rgba(0,0,0,0.7)), url('img/img4.png'); 
        background-size: cover; background-position: center; min-height: 400px; 
    }
  </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-light">
  
  <header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
          <i class="bi bi-gear-wide-connected text-warning"></i> RecambiosPro
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
            
            <?php if (isset($_SESSION['usuario_nombre'])): ?>
                <li class="nav-item dropdown ms-lg-3">
                    <a class="nav-link dropdown-toggle text-white fw-bold" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i> Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-bag me-2"></i> Mis Pedidos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Salir</a></li>
                    </ul>
                </li>

                <?php if (isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true): ?>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-warning btn-sm fw-bold shadow-sm" href="crear.php">
                            <i class="bi bi-plus-lg"></i> Añadir Pieza
                        </a>
                    </li>
                <?php endif; ?>

            <?php else: ?>
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-outline-light btn-sm px-3" href="login.php">Iniciar Sesión</a>
                </li>
                <li class="nav-item ms-2">
                    <a class="btn btn-light btn-sm px-3 text-primary fw-bold" href="registro.php">Registrarse</a>
                </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <section class="hero-section d-flex align-items-center text-white text-center">
      <div class="container">
        <h1 class="display-4 fw-bold mb-3">Tu Recambio al Instante</h1>
        <div class="card p-2 mx-auto shadow-lg border-0" style="max-width: 600px; background: rgba(255,255,255,0.95);">
          <form class="d-flex gap-2" method="GET">
            <input class="form-control form-control-lg border-0" type="search" name="s" placeholder="Buscar pieza..." value="<?php echo htmlspecialchars($busqueda); ?>">
            <button class="btn btn-primary px-4" type="submit">Buscar</button>
          </form>
        </div>
      </div>
    </section>

    <section class="container py-5">
      <div class="row g-4">
        <?php foreach ($productos as $prod): ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
          <div class="card h-100 shadow-sm border-0 card-hover bg-white">
            <div class="bg-light text-center py-4 border-bottom position-relative">
                <i class="bi bi-tools text-secondary opacity-25" style="font-size: 4rem;"></i>
                <span class="badge bg-dark position-absolute top-0 start-0 m-2">
                    <?php echo htmlspecialchars($prod['cat_nombre'] ?? 'General'); ?>
                </span>
            </div>

            <div class="card-body d-flex flex-column p-3">
              <h5 class="card-title h6 fw-bold mb-1"><?php echo htmlspecialchars($prod['nombre']); ?></h5>
              <div class="d-flex justify-content-between align-items-center mb-3 mt-auto">
                <span class="text-primary fw-bold fs-5"><?php echo number_format($prod['precio'], 2); ?> €</span>
                
                <?php if(($prod['stock'] ?? 0) > 0): ?>
                    <span class="badge bg-success-subtle text-success">Stock</span>
                <?php else: ?>
                    <span class="badge bg-danger-subtle text-danger">Agotado</span>
                <?php endif; ?>
              </div>
              <form action="agregar_carrito.php" method="POST" class="w-100 mb-2">
                  <input type="hidden" name="id_producto" value="<?php echo $prod['id']; ?>">
                  <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" <?php echo (($prod['stock'] ?? 0) <= 0) ? 'disabled' : ''; ?>>
                      <i class="bi bi-cart-plus me-1"></i> Añadir al carro
                  </button>
              </form>
              <?php if (isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true): ?>
                  <div class="d-flex justify-content-end border-top pt-2 mt-2">
                      <a href="editar.php?id=<?php echo $prod['id']; ?>" class="text-secondary me-3" title="Editar"><i class="bi bi-pencil-square"></i></a>
                      <a href="borrar.php?id=<?php echo $prod['id']; ?>" class="text-danger" title="Borrar" onclick="return confirm('¿Seguro?')"><i class="bi bi-trash3"></i></a>
                  </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
  </main>

  <footer class="bg-dark text-white py-4 mt-auto">
    <div class="container text-center">
      <p class="small text-secondary mb-0">&copy; 2026 RecambiosPro - Panel de Control Conectado</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>