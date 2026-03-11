<?php
$pagina_titulo = "Inicio | RecambiosPro - Tu tienda de repuestos";
require 'db.php';
include 'header.php';

try {
    // Solo obtenemos las 4 ÚLTIMAS novedades para el escaparate
    $sql = "SELECT p.*, c.nombre as cat_nombre 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            ORDER BY p.id DESC LIMIT 4";
    $stmt = $pdo->query($sql);
    $novedades = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<main>
  <section class="hero-section d-flex align-items-center text-white text-center position-relative" style="background: linear-gradient(rgba(0,36,107,0.8), rgba(0,0,0,0.8)), url('img/img4.png'); background-size: cover; background-position: center; min-height: 500px;">
    <div class="container z-1">
      <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill fw-bold text-uppercase tracking-wide">Calidad OEM Garantizada</span>
      <h1 class="display-3 fw-bold mb-3 text-shadow">El recambio exacto,<br>al mejor precio.</h1>
      <p class="lead mb-4 text-light opacity-75">Encuentra piezas compatibles para tu vehículo en segundos.</p>
      
      <div class="card p-2 mx-auto shadow-lg border-0" style="max-width: 650px; background: rgba(255,255,255,0.95); border-radius: 50px;">
        <form action="catalogo.php" method="GET" class="d-flex gap-2">
          <input class="form-control border-0 bg-transparent px-4 shadow-none" type="search" name="s" placeholder="Ej: Pastillas de freno, filtro de aceite..." required style="border-radius: 50px;">
          <button class="btn btn-primary px-4 fw-bold rounded-pill" type="submit" style="min-width: 120px;"><i class="bi bi-search me-2"></i>Buscar</button>
        </form>
      </div>
    </div>
  </section>

  <section class="bg-white py-5 border-bottom">
      <div class="container">
          <div class="row text-center g-4">
              <div class="col-6 col-md-3">
                  <i class="bi bi-truck display-5 text-primary mb-3"></i>
                  <h6 class="fw-bold">Envío en 24/48h</h6>
                  <p class="text-muted small mb-0">Para pedidos en península</p>
              </div>
              <div class="col-6 col-md-3">
                  <i class="bi bi-shield-check display-5 text-primary mb-3"></i>
                  <h6 class="fw-bold">Garantía de 2 años</h6>
                  <p class="text-muted small mb-0">En todas nuestras piezas</p>
              </div>
              <div class="col-6 col-md-3">
                  <i class="bi bi-arrow-return-left display-5 text-primary mb-3"></i>
                  <h6 class="fw-bold">Devolución Fácil</h6>
                  <p class="text-muted small mb-0">Tienes 30 días para cambios</p>
              </div>
              <div class="col-6 col-md-3">
                  <i class="bi bi-headset display-5 text-primary mb-3"></i>
                  <h6 class="fw-bold">Soporte Técnico</h6>
                  <p class="text-muted small mb-0">Te ayudamos a elegir</p>
              </div>
          </div>
      </div>
  </section>

  <section class="container py-5 mt-4">
      <div class="text-center mb-5">
          <h2 class="fw-bold">Explora por Categorías</h2>
          <p class="text-muted">Encuentra rápidamente lo que tu coche necesita</p>
      </div>
      
      <div class="row g-4">
          <div class="col-md-4">
              <a href="catalogo.php?categoria=1" class="text-decoration-none">
                  <div class="card border-0 shadow-sm text-center py-5 card-hover bg-primary text-white" style="border-radius: 20px;">
                      <i class="bi bi-droplet-half display-3 mb-3 text-warning"></i>
                      <h4 class="fw-bold mb-0">Aceites y Líquidos</h4>
                  </div>
              </a>
          </div>
          <div class="col-md-4">
              <a href="catalogo.php?categoria=2" class="text-decoration-none">
                  <div class="card border-0 shadow-sm text-center py-5 card-hover bg-dark text-white" style="border-radius: 20px;">
                      <i class="bi bi-disc display-3 mb-3 text-info"></i>
                      <h4 class="fw-bold mb-0">Sistema de Frenos</h4>
                  </div>
              </a>
          </div>
          <div class="col-md-4">
              <a href="catalogo.php?categoria=3" class="text-decoration-none">
                  <div class="card border-0 shadow-sm text-center py-5 card-hover bg-secondary text-white" style="border-radius: 20px;">
                      <i class="bi bi-lightning-charge display-3 mb-3 text-warning"></i>
                      <h4 class="fw-bold mb-0">Baterías y Eléctrico</h4>
                  </div>
              </a>
          </div>
      </div>
  </section>

  <section class="bg-light py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-end mb-4">
          <div>
              <h2 class="fw-bold mb-0">Últimas Novedades</h2>
              <p class="text-muted mb-0">Recién llegados a nuestro almacén</p>
          </div>
          <a href="catalogo.php" class="btn btn-outline-primary rounded-pill fw-bold d-none d-sm-inline-block">Ver todo el catálogo <i class="bi bi-arrow-right"></i></a>
      </div>

      <div class="row g-4">
        <?php foreach ($novedades as $prod): ?>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card h-100 shadow-sm border-0 card-hover bg-white" style="border-radius: 15px; overflow: hidden;">
            <div class="bg-white text-center border-bottom position-relative" style="height: 220px;">
                <?php $foto = !empty($prod['imagen']) ? 'img/productos/' . $prod['imagen'] : 'img/productos/placeholder.png'; ?>
                <img src="<?php echo $foto; ?>" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($prod['nombre']); ?>">
                <span class="badge bg-dark position-absolute top-0 start-0 m-3 shadow-sm rounded-pill px-3 py-2"><?php echo htmlspecialchars($prod['cat_nombre'] ?? 'General'); ?></span>
                
                <span class="badge bg-danger position-absolute top-0 end-0 m-3 shadow-sm rounded-pill px-3 py-2"><i class="bi bi-star-fill me-1"></i>Nuevo</span>
            </div>
            
            <div class="card-body d-flex flex-column p-4">
              <h5 class="card-title h6 fw-bold mb-2"><?php echo htmlspecialchars($prod['nombre']); ?></h5>
              <div class="d-flex justify-content-between align-items-center mb-4 mt-auto">
                <span class="text-primary fw-bold fs-4"><?php echo number_format($prod['precio'], 2); ?> €</span>
                <?php if(($prod['stock'] ?? 0) > 0): ?>
                    <span class="small text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>En stock</span>
                <?php else: ?>
                    <span class="small text-danger fw-bold"><i class="bi bi-x-circle-fill me-1"></i>Agotado</span>
                <?php endif; ?>
              </div>
              
              <form action="agregar_carrito.php" method="POST" class="w-100">
                  <input type="hidden" name="id_producto" value="<?php echo $prod['id']; ?>">
                  <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm rounded-pill py-2" <?php echo (($prod['stock'] ?? 0) <= 0) ? 'disabled' : ''; ?>>
                      <i class="bi bi-cart-plus me-2"></i> Añadir al carro
                  </button>
              </form>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      
      <div class="text-center mt-4 d-block d-sm-none">
          <a href="catalogo.php" class="btn btn-outline-primary rounded-pill w-100 fw-bold py-2">Ver todo el catálogo</a>
      </div>
    </div>
  </section>
</main>

<?php include 'footer.php'; ?>