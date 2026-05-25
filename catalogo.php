<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pagina_titulo = "Catálogo | AutoStock";
require_once 'includes/supabase_api.php'; 
include_once 'includes/header.php'; 
$cat_filtro = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;
$busqueda = trim($_GET['s'] ?? '');
$marca_filtro = trim($_GET['marca'] ?? '');
$modelo_filtro = trim($_GET['modelo'] ?? '');
$pagina = max(1, (int)($_GET['p'] ?? 1)); 
$coche_sesion_id = $_SESSION['coche_activo_id'] ?? null;
$coche_sesion_nombre = $_SESSION['coche_activo_nombre'] ?? null;
if (isset($_GET['quitar_sesion_coche'])) {
    unset($_SESSION['coche_activo_id']);
    unset($_SESSION['coche_activo_nombre']);
    $coche_sesion_id = null;
    $coche_sesion_nombre = null;
    header("Location: catalogo.php");
    exit;
}
$por_pagina = 9; 
$offset = ($pagina - 1) * $por_pagina;
$filtrar_por_coche = ($marca_filtro !== '' && $modelo_filtro !== '');
$filtrar_por_sesion = ($coche_sesion_id !== null && !$filtrar_por_coche);
$producto_ids_filtrados = [];
if ($filtrar_por_sesion) {
    $cp_res = consultaSupabase("coche_producto?select=producto_id&coche_id=eq.{$coche_sesion_id}");
    if (!empty($cp_res) && !isset($cp_res['error'])) {
        $producto_ids_filtrados = array_column($cp_res, 'producto_id');
    }
} elseif ($filtrar_por_coche) {
    $endpoint_coches = "coches?select=id&marca=eq." . rawurlencode($marca_filtro) . "&modelo=eq." . rawurlencode($modelo_filtro);
    $coches_res = consultaSupabase($endpoint_coches);
    if (!empty($coches_res) && !isset($coches_res['error'])) {
        $coche_ids = implode(',', array_column($coches_res, 'id'));
        $cp_res = consultaSupabase("coche_producto?select=producto_id&coche_id=in.({$coche_ids})");
        if (!empty($cp_res) && !isset($cp_res['error'])) {
            $producto_ids_filtrados = array_column($cp_res, 'producto_id');
        }
    }
}
$endpoint = "productos?select=*,categorias(nombre)&order=id.desc&limit={$por_pagina}&offset={$offset}";
if ($cat_filtro) {
    $endpoint .= "&categoria_id=eq.{$cat_filtro}";
}
if ($busqueda) {
    $s = rawurlencode("%{$busqueda}%");
    $endpoint .= "&or=(nombre.ilike.{$s},descripcion.ilike.{$s})";
}
if ($filtrar_por_coche || $filtrar_por_sesion) {
    $ids = empty($producto_ids_filtrados) ? "0" : implode(',', $producto_ids_filtrados);
    $endpoint .= "&id=in.({$ids})"; 
}
$res_productos = consultaSupabase($endpoint);
$productos = (!empty($res_productos) && !isset($res_productos['error'])) ? $res_productos : [];
foreach ($productos as &$p) {
    $p['cat_nombre'] = $p['categorias']['nombre'] ?? 'Desconocida';
}
unset($p); 
$res_categorias = consultaSupabase("categorias?select=*&order=nombre.asc");
$categorias = (!empty($res_categorias) && !isset($res_categorias['error'])) ? $res_categorias : [];
$res_marcas = consultaSupabase("coches?select=marca&order=marca.asc");
$marcas_unicas = !empty($res_marcas) ? array_unique(array_column($res_marcas, 'marca')) : [];
$modelos_pre_cargados = [];
if ($marca_filtro !== '') {
    $res_modelos = consultaSupabase("coches?select=modelo&marca=eq." . rawurlencode($marca_filtro) . "&order=modelo.asc");
    $modelos_pre_cargados = !empty($res_modelos) ? array_unique(array_column($res_modelos, 'modelo')) : [];
}
$params_filtro = [];
if ($cat_filtro)    $params_filtro['categoria'] = $cat_filtro;
if ($busqueda)      $params_filtro['s']         = $busqueda;
if ($marca_filtro)  $params_filtro['marca']     = $marca_filtro;
if ($modelo_filtro) $params_filtro['modelo']    = $modelo_filtro;
$url_params = !empty($params_filtro) ? '&' . http_build_query($params_filtro) : '';
?>
<div class="container py-5">
  <div class="row">
    <aside class="col-lg-3 mb-4">
      <div class="filter-sidebar shadow-sm p-3 bg-white rounded mb-4">
        <h5 class="fw-bold mb-3">Filtro por Vehículo</h5>
        <div class="mb-3">
            <label class="small text-muted mb-1">Marca</label>
            <select id="select-marca" class="form-select shadow-none">
                <option value="">Todas las marcas</option>
                <?php foreach ($marcas_unicas as $marca): ?>
                    <option value="<?= htmlspecialchars($marca) ?>" <?= ($marca_filtro === $marca) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($marca) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="small text-muted mb-1">Modelo</label>
            <select id="select-modelo" class="form-select shadow-none" <?= empty($modelos_pre_cargados) ? 'disabled' : '' ?>>
                <option value="">Primero elige marca</option>
                <?php foreach ($modelos_pre_cargados as $modelo): ?>
                    <option value="<?= htmlspecialchars($modelo) ?>" <?= ($modelo_filtro === $modelo) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($modelo) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button id="btn-filtrar-coche" class="btn btn-naranja w-100 fw-bold"><i class="bi bi-funnel me-1"></i> Filtrar Recambios</button>
        <?php if($filtrar_por_coche): ?>
            <a href="catalogo.php" class="btn btn-outline-danger w-100 mt-2 btn-sm">Quitar Filtro</a>
        <?php endif; ?>
      </div>
      <div class="filter-sidebar shadow-sm p-3 bg-white rounded mb-4">
        <h5 class="fw-bold mb-3">Categorías</h5>
        <div class="list-group list-group-flush">
          <a href="catalogo.php" class="list-group-item list-group-item-action <?= !$cat_filtro ? 'active fw-bold' : '' ?>">Todas</a>
          <?php foreach ($categorias as $cat): ?>
            <?php
              $cat_url = "catalogo.php?categoria=" . urlencode($cat['id']);
              if ($marca_filtro) $cat_url .= "&marca=" . urlencode($marca_filtro);
              if ($modelo_filtro) $cat_url .= "&modelo=" . urlencode($modelo_filtro);
            ?>
            <a href="<?= htmlspecialchars($cat_url) ?>" class="list-group-item list-group-item-action <?= $cat_filtro == $cat['id'] ? 'active fw-bold' : '' ?>">
              <?= htmlspecialchars($cat['nombre']) ?>
            </a>
          <?php endforeach; ?>
        </div>
        <hr>
        <form action="catalogo.php" method="GET" class="mt-3">
          <?php if($cat_filtro): ?><input type="hidden" name="categoria" value="<?= htmlspecialchars($cat_filtro) ?>"><?php endif; ?>
          <?php if($marca_filtro): ?><input type="hidden" name="marca" value="<?= htmlspecialchars($marca_filtro) ?>"><?php endif; ?>
          <?php if($modelo_filtro): ?><input type="hidden" name="modelo" value="<?= htmlspecialchars($modelo_filtro) ?>"><?php endif; ?>
          <h5 class="fw-bold mb-3">Buscar</h5>
          <div class="input-group">
              <input type="text" name="s" class="form-control shadow-none" placeholder="Nombre o ref..." value="<?= htmlspecialchars($busqueda) ?>">
              <button class="btn btn-naranja" type="submit"><i class="bi bi-search"></i></button>
          </div>
        </form>
      </div>
    </aside>
    <main class="col-lg-9">
      <?php if ($filtrar_por_sesion): ?>
      <div class="alert alert-warning border-naranja bg-light shadow-sm d-flex justify-content-between align-items-center rounded-4 mb-4">
          <div>
              <h5 class="fw-bold mb-1 text-naranja"><i class="bi bi-car-front-fill me-2"></i> Mostrando repuestos para tu vehículo</h5>
              <p class="mb-0 text-muted small">Catálogo filtrado automáticamente para: <strong><?= htmlspecialchars($coche_sesion_nombre) ?></strong></p>
          </div>
          <a href="?quitar_sesion_coche=1" class="btn btn-sm btn-outline-naranja rounded-pill px-3 fw-bold">Quitar Filtro</a>
      </div>
      <?php endif; ?>
      <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="fw-bold h4 mb-0">
            <?php 
                if ($filtrar_por_coche) echo "Recambios para " . htmlspecialchars("$marca_filtro $modelo_filtro");
                elseif ($filtrar_por_sesion) echo "Tus Recambios Compatibles";
                else echo "Nuestro Catálogo";
            ?>
          </h2>
          <span class="text-muted small">Mostrando <?= count($productos) ?> productos</span>
      </div>
      <div class="row g-4">
        <?php if (!empty($productos)): ?>
          <?php foreach ($productos as $p): ?>
            <?php include 'includes/tarjeta_producto.php'; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="text-center py-5 w-100">
              <i class="bi bi-tools display-1 text-muted opacity-25"></i>
              <h4 class="mt-3 text-muted">No se encontraron productos con estos filtros.</h4>
              <p class="text-muted">Prueba a buscar otro vehículo o elimina algunos filtros.</p>
          </div>
        <?php endif; ?>
      </div>
      <nav class="mt-5">
        <ul class="pagination justify-content-center">
          <li class="page-item <?= ($pagina <= 1) ? 'disabled' : '' ?>">
              <a class="page-link rounded-start-pill" href="?p=<?= $pagina - 1 ?><?= htmlspecialchars($url_params) ?>">Anterior</a>
          </li>
          <li class="page-item active"><span class="page-link"><?= $pagina ?></span></li>
          <li class="page-item <?= (count($productos) < $por_pagina) ? 'disabled' : '' ?>">
              <a class="page-link rounded-end-pill" href="?p=<?= $pagina + 1 ?><?= htmlspecialchars($url_params) ?>">Siguiente</a>
          </li>
        </ul>
      </nav>
    </main>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const selectMarca = document.getElementById('select-marca');
    const selectModelo = document.getElementById('select-modelo');
    const btnFiltrar = document.getElementById('btn-filtrar-coche');
    selectMarca.addEventListener('change', async function() {
        const marca = this.value;
        selectModelo.innerHTML = '<option value="">Cargando modelos...</option>';
        selectModelo.disabled = true;
        if (!marca) {
            selectModelo.innerHTML = '<option value="">Primero elige marca</option>';
            return;
        }
        try {
            const res = await fetch(`scripts/obtener_modelos.php?marca=${encodeURIComponent(marca)}`);
            const data = await res.json();
            selectModelo.innerHTML = '<option value="">Selecciona Modelo</option>';
            const modelosUnicos = [...new Set(data.map(i => i.modelo))];
            modelosUnicos.forEach(modelo => {
                selectModelo.appendChild(new Option(modelo, modelo));
            });
            selectModelo.disabled = false;
        } catch (error) {
            console.error('Error cargando modelos:', error);
            selectModelo.innerHTML = '<option value="">Error al cargar</option>';
        }
    });
    btnFiltrar.addEventListener('click', () => {
        const marca = selectMarca.value;
        const modelo = selectModelo.value;
        const url = new URL(window.location.href);
        url.searchParams.delete('p'); // Resetear paginación al filtrar
        if (marca && modelo) {
            url.searchParams.set('marca', marca);
            url.searchParams.set('modelo', modelo);
            window.location.assign(url);
        } else if (!marca) {
            url.searchParams.delete('marca');
            url.searchParams.delete('modelo');
            window.location.assign(url);
        } else {
            alert("Por favor, selecciona un modelo para filtrar.");
        }
    });
});
</script>
<?php include_once 'includes/footer.php'; ?>