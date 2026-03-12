<?php
$pagina_titulo = "Catálogo | RecambiosPro";
require '../includes/db.php'; 
include '../includes/header.php'; 

$cat_filtro = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;
$busqueda = isset($_GET['s']) ? trim($_GET['s']) : '';
$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$por_pagina = 9; 
$offset = ($pagina - 1) * $por_pagina;

try {
    $query_parts = ["WHERE 1=1"];
    $params = [];

    if ($cat_filtro) {
        $query_parts[] = "AND p.categoria_id = :cat_id";
        $params['cat_id'] = $cat_filtro;
    }
    if ($busqueda) {
        $query_parts[] = "AND (p.nombre LIKE :s OR p.descripcion LIKE :s)";
        $params['s'] = "%$busqueda%";
    }

    $where_sql = implode(" ", $query_parts);
    $sql = "SELECT p.*, c.nombre as cat_nombre 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            $where_sql 
            ORDER BY p.id DESC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $productos = $stmt->fetchAll();
    $categorias = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll();

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>
<link rel="stylesheet" href="../assets/css/Styles">
<div class="container py-5">
  <div class="row">
    
    <aside class="col-lg-3 mb-4">
      <div class="filter-sidebar shadow-sm">
        <h5 class="fw-bold mb-3">Categorías</h5>
        <div class="list-group list-group-flush">
          <a href="catalogo.php" class="list-group-item list-group-item-action <?php echo !$cat_filtro ? 'active fw-bold' : ''; ?>">Todas</a>
          <?php foreach ($categorias as $cat): ?>
            <a href="catalogo.php?categoria=<?php echo $cat['id']; ?>" 
               class="list-group-item list-group-item-action <?php echo $cat_filtro == $cat['id'] ? 'active fw-bold' : ''; ?>">
              <?php echo htmlspecialchars($cat['nombre']); ?>
            </a>
          <?php endforeach; ?>
        </div>
        <hr>
        <form action="catalogo.php" method="GET" class="mt-3">
          <h5 class="fw-bold mb-3">Buscar</h5>
          <div class="input-group">
              <input type="text" name="s" class="form-control shadow-none" placeholder="Nombre..." value="<?php echo htmlspecialchars($busqueda); ?>">
              <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
          </div>
        </form>
      </div>
    </aside>

    <main class="col-lg-9">
      <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="fw-bold h4 mb-0">Nuestro Catálogo</h2>
          <span class="text-muted small">Mostrando <?php echo count($productos); ?> productos</span>
      </div>

      <div class="row g-4">
        <?php if (count($productos) > 0): ?>
          <?php foreach ($productos as $p): ?>
             <?php include '../includes/tarjeta_producto.php'; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="text-center py-5">
              <i class="bi bi-emoji-frown display-1 text-muted opacity-50"></i>
              <h4 class="mt-3 text-muted">No se encontraron productos.</h4>
          </div>
        <?php endif; ?>
      </div>

      <nav class="mt-5">
        <ul class="pagination justify-content-center">
          <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
              <a class="page-link rounded-start-pill" href="?p=<?php echo $pagina-1; ?>&categoria=<?php echo $cat_filtro; ?>&s=<?php echo $busqueda; ?>">Anterior</a>
          </li>
          <li class="page-item active"><span class="page-link"><?php echo $pagina; ?></span></li>
          <li class="page-item <?php echo (count($productos) < $por_pagina) ? 'disabled' : ''; ?>">
              <a class="page-link rounded-end-pill" href="?p=<?php echo $pagina+1; ?>&categoria=<?php echo $cat_filtro; ?>&s=<?php echo $busqueda; ?>">Siguiente</a>
          </li>
        </ul>
      </nav>
      
    </main>
  </div>
</div>

<?php include '../includes/footer.php'; ?>