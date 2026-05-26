<?php
require_once __DIR__ . '/includes/security.php';
initSecureSession();
$pagina_titulo = "Inicio | AutoStock - Tu tienda de repuestos";
require_once 'includes/supabase_api.php';
include_once 'includes/header.php';
$endpoint = "productos?select=*,categorias(nombre)&order=id.desc&limit=3";
$respuesta = consultaSupabase($endpoint);
$novedades = [];
if (isset($respuesta['error'])) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Error al cargar las novedades: " . htmlspecialchars($respuesta['error']) . "</div></div>";
} elseif (is_array($respuesta)) {
    foreach ($respuesta as $item) {
        $item['cat_nombre'] = $item['categorias']['nombre'] ?? 'Desconocida';
        $novedades[] = $item;
    }
}
?>
<?php
$notificaciones = [
    'eliminado' => ['tipo' => 'success', 'icono' => 'ph-fill ph-trash', 'titulo' => '¡Producto eliminado!', 'mensaje' => 'El repuesto se ha borrado correctamente de la base de datos.'],
    'error'     => ['tipo' => 'danger',  'icono' => 'ph-fill ph-warning', 'titulo' => 'Error:', 'mensaje' => 'No se pudo eliminar el producto. Puede que esté asociado a un pedido o coche.'],
    'creado'    => ['tipo' => 'success', 'icono' => 'ph-fill ph-check-circle', 'titulo' => '¡Éxito!', 'mensaje' => 'Producto añadido correctamente.'],
    'editado'   => ['tipo' => 'success', 'icono' => 'ph-fill ph-check-circle', 'titulo' => '¡Éxito!', 'mensaje' => 'Producto actualizado correctamente.'],
    'agregado'  => ['tipo' => 'success', 'icono' => 'ph-fill ph-shopping-cart-simple', 'titulo' => '¡Añadido!', 'mensaje' => 'El producto se ha añadido a tu carrito.'],
];
foreach ($notificaciones as $clave => $datos):
    if (!isset($_GET[$clave])) continue;
?>
<div class="container mt-3">
    <div class="alert alert-<?= $datos['tipo'] ?> alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius: 15px;">
        <i class="<?= $datos['icono'] ?> me-2"></i>
        <strong><?= $datos['titulo'] ?></strong> <?= $datos['mensaje'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php
    break;
endforeach;
?>
<main>
  <section class="hero-section d-flex align-items-center text-white text-center position-relative">
    <div class="container z-1">
      <span class="badge bg-azul-claro text-dark mb-3 px-3 py-2 rounded-pill fw-bold text-uppercase tracking-wide hero-badge">Calidad OEM Garantizada</span>
      <h1 class="display-3 fw-bold mb-3 text-white hero-title">El recambio exacto,<br>al mejor precio.</h1>
      <p class="lead mb-4 text-light opacity-75 hero-subtitle">Encuentra piezas compatibles para tu vehículo en segundos.</p>
      <div class="card p-2 mx-auto shadow-lg border-0 hero-search" style="max-width: 650px; background: rgba(255,255,255,0.95); border-radius: 50px;">
        <form action="catalogo.php" method="GET" class="d-flex gap-2">
          <input class="form-control border-0 bg-transparent px-4 shadow-none" type="search" name="s" placeholder="Ej: Pastillas de freno, filtro de aceite..." required style="border-radius: 50px;">
          <button class="btn btn-naranja px-4 fw-bold rounded-pill" type="submit" style="min-width: 120px;"><i class="ph ph-magnifying-glass me-2"></i>Buscar</button>
        </form>
      </div>
    </div>
  </section>
  <section class="bg-white py-5 border-bottom">
      <div class="container">
          <div class="row text-center g-4">
              <article class="col-6 col-md-3 anim fade-up delay-1">
                  <span class="feature-icon-wrap"><i class="ph ph-truck display-5 text-naranja mb-3 d-block"></i></span>
                  <h6 class="fw-bold titulo-oscuro">Envío en 24/48h</h6>
                  <p class="text-muted small mb-0">Para pedidos en península</p>
              </article>
              <article class="col-6 col-md-3 anim fade-up delay-2">
                  <span class="feature-icon-wrap"><i class="ph ph-shield-check display-5 text-naranja mb-3 d-block"></i></span>
                  <h6 class="fw-bold titulo-oscuro">Garantía de 2 años</h6>
                  <p class="text-muted small mb-0">En todas nuestras piezas</p>
              </article>
              <article class="col-6 col-md-3 anim fade-up delay-3">
                  <span class="feature-icon-wrap"><i class="ph ph-arrow-bend-up-left display-5 text-naranja mb-3 d-block"></i></span>
                  <h6 class="fw-bold titulo-oscuro">Devolución Fácil</h6>
                  <p class="text-muted small mb-0">Tienes 30 días para cambios</p>
              </article>
              <article class="col-6 col-md-3 anim fade-up delay-4">
                  <span class="feature-icon-wrap"><i class="ph ph-headset display-5 text-naranja mb-3 d-block"></i></span>
                  <h6 class="fw-bold titulo-oscuro">Soporte Técnico</h6>
                  <p class="text-muted small mb-0">Te ayudamos a elegir</p>
              </article>
          </div>
      </div>
  </section>
  <section class="container py-5 mt-4">
      <div class="text-center mb-5 anim fade-up">
          <h2 class="fw-bold titulo-oscuro">Explora por Categorías</h2>
          <p class="text-muted">Encuentra rápidamente lo que tu coche necesita</p>
      </div>
      <div class="row g-4">
        <article class="col-md-4 anim scale-in delay-1">
            <a href="catalogo.php?categoria=1" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center py-5 card-hover fondo-suave text-azul-oscuro" style="border-radius: 20px;">
                    <i class="ph ph-drop-half display-3 mb-3 text-naranja"></i>
                    <h4 class="fw-bold mb-0">Aceites y Líquidos</h4>
                </div>
            </a>
        </article>
        <article class="col-md-4 anim scale-in delay-2">
            <a href="catalogo.php?categoria=2" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center py-5 card-hover fondo-suave text-azul-oscuro" style="border-radius: 20px;">
                    <i class="ph ph-disc display-3 mb-3 text-naranja"></i>
                    <h4 class="fw-bold mb-0">Sistema de Frenos</h4>
                </div>
            </a>
        </article>
        <article class="col-md-4 anim scale-in delay-3">
            <a href="catalogo.php?categoria=3" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center py-5 card-hover fondo-suave text-azul-oscuro" style="border-radius: 20px;">
                    <i class="ph ph-lightning display-3 mb-3 text-naranja"></i>
                    <h4 class="fw-bold mb-0">Baterías y Eléctrico</h4>
                </div>
            </a>
        </article>
      </div>
  </section>
  <section class="fondo-suave py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-end mb-4 anim fade-up">
          <div>
              <h2 class="fw-bold mb-0 titulo-oscuro">Últimas Novedades</h2>
              <p class="text-muted mb-0">Recién llegados a nuestro almacén</p>
          </div>
          <a href="catalogo.php" class="btn btn-outline-naranja rounded-pill fw-bold d-none d-sm-inline-block">Ver todo el catálogo <i class="ph ph-arrow-right"></i></a>
      </div>
      <div class="row g-4">
        <?php if (!empty($novedades)): ?>
            <?php foreach ($novedades as $p): ?>
                <?php include 'includes/tarjeta_producto.php'; ?>
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
<?php include_once 'includes/footer.php'; ?>