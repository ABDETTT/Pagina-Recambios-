<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pagina_titulo = "Inicio | RecambiosPro - Tu tienda de repuestos";

require 'supabase_api.php'; 
include 'header.php';

$endpoint = "productos?select=*,categorias(nombre)&order=id.desc&limit=3";
$respuesta = consultaSupabase($endpoint);

$novedades = [];

if (isset($respuesta['error'])) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Error al cargar las novedades: " . $respuesta['error'] . "</div></div>";
} elseif (is_array($respuesta)) {
    foreach ($respuesta as $item) {
        $item['cat_nombre'] = $item['categorias']['nombre'] ?? 'Desconocida';
        $novedades[] = $item;
    }
}
?>
<?php if (isset($_GET['eliminado'])): ?>
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-trash-fill fs-4 me-2"></i>
                <div>
                    <strong>¡Producto eliminado!</strong> El repuesto se ha borrado correctamente de la base de datos.
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius: 15px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Error:</strong> No se pudo eliminar el producto. Puede que esté asociado a un pedido o coche.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>
<main>
  <section class="hero-section d-flex align-items-center text-white text-center position-relative">
    <div class="container z-1">
      <span class="badge bg-azul-claro text-dark mb-3 px-3 py-2 rounded-pill fw-bold text-uppercase tracking-wide">Calidad OEM Garantizada</span>
      <h1 class="display-3 fw-bold mb-3 text-white text-shadow">El recambio exacto,<br>al mejor precio.</h1>
      <p class="lead mb-4 text-light opacity-75">Encuentra piezas compatibles para tu vehículo en segundos.</p>
      
      <div class="card p-2 mx-auto shadow-lg border-0" style="max-width: 650px; background: rgba(255,255,255,0.95); border-radius: 50px;">
        <form action="catalogo.php" method="GET" class="d-flex gap-2">
          <input class="form-control border-0 bg-transparent px-4 shadow-none" type="search" name="s" placeholder="Ej: Pastillas de freno, filtro de aceite..." required style="border-radius: 50px;">
          <button class="btn btn-naranja px-4 fw-bold rounded-pill" type="submit" style="min-width: 120px;"><i class="bi bi-search me-2"></i>Buscar</button>
        </form>
      </div>
    </div>
  </section>

  <section class="bg-white py-5 border-bottom">
      <div class="container">
          <div class="row text-center g-4">
              <div class="col-6 col-md-3">
                  <i class="bi bi-truck display-5 text-naranja mb-3"></i>
                  <h6 class="fw-bold titulo-oscuro">Envío en 24/48h</h6>
                  <p class="text-muted small mb-0">Para pedidos en península</p>
              </div>
              <div class="col-6 col-md-3">
                  <i class="bi bi-shield-check display-5 text-naranja mb-3"></i>
                  <h6 class="fw-bold titulo-oscuro">Garantía de 2 años</h6>
                  <p class="text-muted small mb-0">En todas nuestras piezas</p>
              </div>
              <div class="col-6 col-md-3">
                  <i class="bi bi-arrow-return-left display-5 text-naranja mb-3"></i>
                  <h6 class="fw-bold titulo-oscuro">Devolución Fácil</h6>
                  <p class="text-muted small mb-0">Tienes 30 días para cambios</p>
              </div>
              <div class="col-6 col-md-3">
                  <i class="bi bi-headset display-5 text-naranja mb-3"></i>
                  <h6 class="fw-bold titulo-oscuro">Soporte Técnico</h6>
                  <p class="text-muted small mb-0">Te ayudamos a elegir</p>
              </div>
          </div>
      </div>
  </section>

  <section class="container py-5 mt-4">
      <div class="text-center mb-5">
          <h2 class="fw-bold titulo-oscuro">Explora por Categorías</h2>
          <p class="text-muted">Encuentra rápidamente lo que tu coche necesita</p>
      </div>
      
      <div class="row g-4">
        <div class="col-md-4">
            <a href="catalogo.php?categoria=1" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center py-5 card-hover fondo-suave text-azul-oscuro" style="border-radius: 20px;">
                    <i class="bi bi-droplet-half display-3 mb-3 text-naranja"></i>
                    <h4 class="fw-bold mb-0">Aceites y Líquidos</h4>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="catalogo.php?categoria=2" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center py-5 card-hover fondo-suave text-azul-oscuro" style="border-radius: 20px;">
                    <i class="bi bi-disc display-3 mb-3 text-naranja"></i>
                    <h4 class="fw-bold mb-0">Sistema de Frenos</h4>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="catalogo.php?categoria=3" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center py-5 card-hover fondo-suave text-azul-oscuro" style="border-radius: 20px;">
                    <i class="bi bi-lightning-charge display-3 mb-3 text-naranja"></i>
                    <h4 class="fw-bold mb-0">Baterías y Eléctrico</h4>
                </div>
            </a>
        </div>
      </div>
  </section>

  <section class="fondo-suave py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-end mb-4">
          <div>
              <h2 class="fw-bold mb-0 titulo-oscuro">Últimas Novedades</h2>
              <p class="text-muted mb-0">Recién llegados a nuestro almacén</p>
          </div>
          <a href="catalogo.php" class="btn btn-outline-naranja rounded-pill fw-bold d-none d-sm-inline-block">Ver todo el catálogo <i class="bi bi-arrow-right"></i></a>
      </div>

      <div class="row g-4">
        <?php if (!empty($novedades)): ?>
            <?php foreach ($novedades as $p): ?>
                <?php include 'tarjeta_producto.php'; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted">No hay novedades disponibles en este momento.</p>
        <?php endif; ?>
      </div>
      
      <div class="text-center mt-4 d-block d-sm-none">
          <a href="catalogo.php" class="btn btn-outline-naranja rounded-pill w-100 fw-bold py-2">Ver todo el catálogo</a>
      </div>
    </div>
  </section>
</main>

<?php include 'footer.php'; ?>