<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'includes/supabase_api.php';
$usuario_id = $_SESSION['usuario_id'];
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'solicitar_devolucion') {
        $pedido_id = (int)($_POST['pedido_id'] ?? 0);
        if ($pedido_id) {
            consultaSupabase("pedidos?id=eq.$pedido_id&usuario_id=eq.$usuario_id", "PATCH", ['estado' => 'Devolución Solicitada']);
            $mensaje = "Has solicitado la devolución del pedido #$pedido_id. Nuestro equipo lo revisará.";
        }
    } elseif ($action === 'add_coche') {
        $coche_id = (int)($_POST['coche_id'] ?? 0);
        if ($coche_id) {
            $data = [
                'usuario_id' => $usuario_id,
                'coche_id' => $coche_id,
                'seleccionado_actualmente' => false
            ];
            $res = consultaSupabase("garaje_usuario", "POST", [$data]);
            if (!isset($res['error'])) {
                $mensaje = "Vehículo añadido a tu garaje.";
            } else {
                $mensaje = "Error al añadir el vehículo.";
            }
        }
    } elseif ($action === 'set_coche_activo') {
        $garaje_id = (int)($_POST['garaje_id'] ?? 0);
        $coche_id = (int)($_POST['coche_id'] ?? 0);
        if ($garaje_id && $coche_id) {
            consultaSupabase("garaje_usuario?usuario_id=eq.$usuario_id", "PATCH", ['seleccionado_actualmente' => false]);
            consultaSupabase("garaje_usuario?id=eq.$garaje_id", "PATCH", ['seleccionado_actualmente' => true]);
            $_SESSION['coche_activo_id'] = $coche_id;
            $cocheInfo = consultaSupabase("coches?id=eq.$coche_id");
            if (!empty($cocheInfo) && !isset($cocheInfo['error'])) {
                $_SESSION['coche_activo_nombre'] = $cocheInfo[0]['marca'] . ' ' . $cocheInfo[0]['modelo'];
            }
            $mensaje = "Vehículo activado. El catálogo mostrará piezas compatibles.";
        }
    } elseif ($action === 'quitar_coche_activo') {
        consultaSupabase("garaje_usuario?usuario_id=eq.$usuario_id", "PATCH", ['seleccionado_actualmente' => false]);
        unset($_SESSION['coche_activo_id']);
        unset($_SESSION['coche_activo_nombre']);
        $mensaje = "Filtro de vehículo desactivado.";
    } elseif ($action === 'eliminar_coche') {
        $garaje_id = (int)($_POST['garaje_id'] ?? 0);
        if ($garaje_id) {
            consultaSupabase("garaje_usuario?id=eq.$garaje_id&usuario_id=eq.$usuario_id", "DELETE");
            if (isset($_SESSION['coche_activo_id'])) {
                unset($_SESSION['coche_activo_id']);
                unset($_SESSION['coche_activo_nombre']);
            }
            $mensaje = "Vehículo eliminado del garaje.";
        }
    }
}
$res_pedidos = consultaSupabase("pedidos?usuario_id=eq.$usuario_id&order=id.desc");
$pedidos = (!empty($res_pedidos) && !isset($res_pedidos['error'])) ? $res_pedidos : [];
$res_garaje = consultaSupabase("garaje_usuario?usuario_id=eq.$usuario_id");
$garaje = (!empty($res_garaje) && !isset($res_garaje['error'])) ? $res_garaje : [];
$res_coches = consultaSupabase("coches?order=marca.asc");
$coches = (!empty($res_coches) && !isset($res_coches['error'])) ? $res_coches : [];
$coches_map = [];
foreach ($coches as $c) {
    $coches_map[$c['id']] = $c;
}
$pagina_titulo = "Panel de Cliente | AutoStock";
include_once 'includes/header.php';
?>
<main class="container py-5">
    <header class="mb-4">
        <h1 class="fw-bold titulo-oscuro"><i class="bi bi-person-badge text-naranja me-2"></i> Mi Área de Cliente</h1>
        <p class="text-muted">Gestiona tus pedidos y tu garaje virtual desde aquí.</p>
    </header>
    <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['pedido_exito'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 py-4" role="alert" style="border-left: 5px solid #198754;">
            <div class="d-flex align-items-center">
                <i class="bi bi-bag-check-fill display-5 me-3 text-success"></i>
                <div>
                    <h4 class="alert-heading fw-bold mb-1">¡Pedido Realizado con Éxito!</h4>
                    <p class="mb-0">Gracias por tu compra. Puedes ver el estado de tu pedido en la pestaña "Mis Pedidos".</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <ul class="nav nav-tabs nav-fill mb-4 fw-bold" id="clientTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active text-azul-oscuro" id="dash-tab" data-bs-toggle="tab" data-bs-target="#dash" type="button" role="tab"><i class="bi bi-house me-1"></i> Resumen</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-azul-oscuro" id="garaje-tab" data-bs-toggle="tab" data-bs-target="#garaje" type="button" role="tab"><i class="bi bi-car-front me-1"></i> Mi Garaje</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-azul-oscuro" id="pedidos-tab" data-bs-toggle="tab" data-bs-target="#pedidos" type="button" role="tab"><i class="bi bi-box-seam me-1"></i> Mis Pedidos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-azul-oscuro" id="perfil-tab" data-bs-toggle="tab" data-bs-target="#perfil" type="button" role="tab"><i class="bi bi-person me-1"></i> Perfil</button>
        </li>
    </ul>
    <div class="tab-content" id="clientTabsContent">
        <div class="tab-pane fade show active" id="dash" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4 text-center">
                            <i class="bi bi-car-front display-4 text-naranja mb-3"></i>
                            <h4 class="fw-bold">Vehículo Activo</h4>
                            <?php if (isset($_SESSION['coche_activo_nombre'])): ?>
                                <p class="text-success fw-bold fs-5 mb-1"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_SESSION['coche_activo_nombre']) ?></p>
                                <p class="text-muted small">El catálogo está filtrando piezas compatibles con este vehículo.</p>
                                <a href="catalogo.php" class="btn btn-naranja rounded-pill mt-2">Ir al Catálogo</a>
                            <?php else: ?>
                                <p class="text-muted">No has seleccionado ningún vehículo activo.</p>
                                <button class="btn btn-outline-naranja rounded-pill mt-2" onclick="document.getElementById('garaje-tab').click()">Configurar Garaje</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100 bg-azul-oscuro text-white">
                        <div class="card-body p-4 text-center">
                            <i class="bi bi-box-seam display-4 mb-3 text-light opacity-75"></i>
                            <h4 class="fw-bold">Tus Pedidos</h4>
                            <p class="fs-5 mb-1"><?= count($pedidos) ?> pedidos realizados</p>
                            <p class="small opacity-75">Gracias por confiar en AutoStock.</p>
                            <button class="btn btn-light text-azul-oscuro fw-bold rounded-pill mt-2" onclick="document.getElementById('pedidos-tab').click()">Ver Historial</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="garaje" role="tabpanel">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="fw-bold mb-3 titulo-oscuro">Vehículos Guardados</h4>
                    <?php if (empty($garaje)): ?>
                        <div class="alert alert-light border shadow-sm text-center py-5">
                            <i class="bi bi-cone-striped display-4 text-muted mb-3"></i>
                            <h5>Tu garaje está vacío</h5>
                            <p class="text-muted mb-0">Añade tu coche para encontrar repuestos compatibles fácilmente.</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($garaje as $g):
                                $coche = $coches_map[$g['coche_id']] ?? null;
                                if(!$coche) continue;
                                $esActivo = $g['seleccionado_actualmente'];
                            ?>
                                <article class="col-md-6">
                                    <div class="card border-2 <?= $esActivo ? 'border-naranja shadow' : 'border-light shadow-sm' ?> rounded-4 h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="fw-bold mb-0"><?= htmlspecialchars($coche['marca'] . ' ' . $coche['modelo']) ?></h5>
                                                <?php if($esActivo): ?>
                                                    <span class="badge bg-naranja"><i class="bi bi-check2-circle"></i> Activo</span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-muted small mb-4">Año: <?= htmlspecialchars($coche['anio']) ?></p>
                                            <div class="d-flex gap-2">
                                                <?php if(!$esActivo): ?>
                                                    <form method="POST" class="m-0 flex-grow-1">
                                                        <input type="hidden" name="action" value="set_coche_activo">
                                                        <input type="hidden" name="garaje_id" value="<?= $g['id'] ?>">
                                                        <input type="hidden" name="coche_id" value="<?= $g['coche_id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-azul-oscuro w-100">Seleccionar</button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" class="m-0 flex-grow-1">
                                                        <input type="hidden" name="action" value="quitar_coche_activo">
                                                        <button type="submit" class="btn btn-sm btn-naranja w-100 text-white">Quitar Filtro</button>
                                                    </form>
                                                <?php endif; ?>
                                                <form method="POST" class="m-0" onsubmit="return confirm('¿Seguro que deseas quitar este coche del garaje?');">
                                                    <input type="hidden" name="action" value="eliminar_coche">
                                                    <input type="hidden" name="garaje_id" value="<?= $g['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-light text-danger border"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-4 bg-light">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3 titulo-oscuro"><i class="bi bi-plus-circle text-naranja me-2"></i> Añadir Vehículo</h5>
                            <form method="POST">
                                <input type="hidden" name="action" value="add_coche">
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Selecciona tu coche de la base de datos:</label>
                                    <select name="coche_id" class="form-select" required>
                                        <option value="">-- Seleccionar --</option>
                                        <?php foreach($coches as $c): ?>
                                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['marca'] . ' ' . $c['modelo'] . ' (' . $c['anio'] . ')') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-azul-oscuro w-100 fw-bold">Guardar en Garaje</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="pedidos" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <?php if (empty($pedidos)): ?>
                        <p class="text-muted text-center py-5 mb-0">Aún no has realizado ninguna compra.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Nº Pedido</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th class="text-end pe-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pedidos as $pedido): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-muted">#<?= $pedido['id'] ?></td>
                                            <td><?= htmlspecialchars($pedido['fecha'] ?? 'N/A') ?></td>
                                            <td class="fw-bold text-naranja"><?= number_format($pedido['total'] ?? 0, 2) ?> €</td>
                                            <td>
                                                <?php
                                                    $estado = $pedido['estado'] ?? 'Pendiente';
                                                    $badge_class = 'bg-secondary';
                                                    if($estado === 'Entregado') $badge_class = 'bg-success';
                                                    if($estado === 'Enviado') $badge_class = 'bg-primary';
                                                    if($estado === 'Devolución Solicitada') $badge_class = 'bg-warning text-dark';
                                                    if($estado === 'Cancelado') $badge_class = 'bg-danger';
                                                ?>
                                                <span class="badge <?= $badge_class ?>"><?= htmlspecialchars($estado) ?></span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <?php if($estado === 'Entregado'): ?>
                                                    <form method="POST" class="d-inline-block" onsubmit="return confirm('¿Solicitar devolución de este pedido?');">
                                                        <input type="hidden" name="action" value="solicitar_devolucion">
                                                        <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Devolver Pedido">
                                                            <i class="bi bi-arrow-counterclockwise"></i> Devolver
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="perfil" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4" style="max-width: 500px; margin: 0 auto;">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-person-circle display-1 text-azul-claro mb-3"></i>
                    <h3 class="fw-bold titulo-oscuro"><?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?></h3>
                    <p class="text-muted"><?= htmlspecialchars($_SESSION['email'] ?? 'correo@ejemplo.com') ?></p>
                    <hr class="my-4">
                    <p class="small text-muted mb-4">Para modificar tus datos personales o contraseña, por favor contacta con soporte técnico.</p>
                    <a href="logout.php" class="btn btn-outline-danger w-100 rounded-pill fw-bold"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include_once 'includes/footer.php'; ?>
