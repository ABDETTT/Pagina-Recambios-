<?php
require_once __DIR__ . '/includes/security.php';
initSecureSession();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: auth/login.php");
    exit;
}
require_once 'includes/supabase_api.php';
require_once 'includes/config.php';
$usuario_id = $_SESSION['usuario_id'];

function garajePatch($endpoint, $body) {
    $url = SUPABASE_URL . "/rest/v1/" . $endpoint;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: " . SUPABASE_SERVICE_KEY,
        "Authorization: Bearer " . SUPABASE_SERVICE_KEY,
        "Content-Type: application/json",
        "Prefer: return=minimal"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_exec($ch);
    curl_close($ch);
}
$mensaje = '';
$tipo_mensaje = 'success';
$error_perfil = '';
$error_pass = '';
$active_tab = 'dash';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfCheck()) {
        $mensaje = "Solicitud no válida. Recarga la página e inténtalo de nuevo.";
        $tipo_mensaje = 'danger';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'solicitar_devolucion') {
            $active_tab = 'pedidos';
            $pedido_id = (int)($_POST['pedido_id'] ?? 0);
            if ($pedido_id) {
                consultaSupabase("pedidos?id=eq.$pedido_id&usuario_id=eq.$usuario_id", "PATCH", ['estado' => 'Devolución Solicitada']);
                $mensaje = "Has solicitado la devolución del pedido #$pedido_id. Nuestro equipo lo revisará.";
            }

        } elseif ($action === 'add_coche') {
            $active_tab = 'garaje';
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
                    $tipo_mensaje = 'danger';
                }
            }

        } elseif ($action === 'set_coche_activo') {
            $active_tab = 'garaje';
            $garaje_id = (int)($_POST['garaje_id'] ?? 0);
            $coche_id = (int)($_POST['coche_id'] ?? 0);
            if ($garaje_id && $coche_id) {
                garajePatch("garaje_usuario?usuario_id=eq.$usuario_id", ['seleccionado_actualmente' => false]);
                garajePatch("garaje_usuario?id=eq.$garaje_id", ['seleccionado_actualmente' => true]);
                $_SESSION['coche_activo_id'] = $coche_id;
                $cocheInfo = consultaSupabase("coches?id=eq.$coche_id");
                if (!empty($cocheInfo) && !isset($cocheInfo['error'])) {
                    $_SESSION['coche_activo_nombre'] = $cocheInfo[0]['marca'] . ' ' . $cocheInfo[0]['modelo'];
                }
                $mensaje = "Vehículo activado. El catálogo mostrará piezas compatibles.";
            }

        } elseif ($action === 'quitar_coche_activo') {
            $active_tab = 'garaje';
            garajePatch("garaje_usuario?usuario_id=eq.$usuario_id", ['seleccionado_actualmente' => false]);
            unset($_SESSION['coche_activo_id']);
            unset($_SESSION['coche_activo_nombre']);
            $mensaje = "Filtro de vehículo desactivado.";

        } elseif ($action === 'eliminar_coche') {
            $active_tab = 'garaje';
            $garaje_id = (int)($_POST['garaje_id'] ?? 0);
            if ($garaje_id) {
                consultaSupabase("garaje_usuario?id=eq.$garaje_id&usuario_id=eq.$usuario_id", "DELETE");
                if (isset($_SESSION['coche_activo_id'])) {
                    unset($_SESSION['coche_activo_id']);
                    unset($_SESSION['coche_activo_nombre']);
                }
                $mensaje = "Vehículo eliminado del garaje.";
            }

        } elseif ($action === 'actualizar_perfil') {
            $active_tab = 'perfil';
            $nombre   = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            if (empty($nombre) || empty($apellido)) {
                $error_perfil = "El nombre y el apellido son obligatorios.";
            } else {
                $res = consultaSupabase("perfiles?id=eq.$usuario_id", "PATCH", ['nombre' => $nombre, 'apellido' => $apellido]);
                if (!isset($res['error'])) {
                    $_SESSION['nombre']   = $nombre;
                    $_SESSION['apellido'] = $apellido;
                    $mensaje = "Tus datos personales han sido actualizados.";
                } else {
                    $error_perfil = "No se pudo actualizar el perfil. Inténtalo de nuevo.";
                }
            }

        } elseif ($action === 'cambiar_password') {
            $active_tab = 'perfil';
            $pass_actual    = $_POST['pass_actual']    ?? '';
            $pass_nueva     = $_POST['pass_nueva']     ?? '';
            $pass_confirmar = $_POST['pass_confirmar'] ?? '';
            if (empty($pass_actual) || empty($pass_nueva) || empty($pass_confirmar)) {
                $error_pass = "Completa todos los campos de contraseña.";
            } elseif (strlen($pass_nueva) < 8) {
                $error_pass = "La nueva contraseña debe tener al menos 8 caracteres.";
            } elseif ($pass_nueva !== $pass_confirmar) {
                $error_pass = "La nueva contraseña y su confirmación no coinciden.";
            } else {
                $res_perfil = consultaSupabase("perfiles?id=eq.$usuario_id&select=password");
                if (!empty($res_perfil) && !isset($res_perfil['error']) && isset($res_perfil[0]['password'])) {
                    if (!password_verify($pass_actual, $res_perfil[0]['password'])) {
                        $error_pass = "La contraseña actual no es correcta.";
                    } else {
                        $nuevo_hash = password_hash($pass_nueva, PASSWORD_BCRYPT);
                        $res = consultaSupabase("perfiles?id=eq.$usuario_id", "PATCH", ['password' => $nuevo_hash]);
                        if (!isset($res['error'])) {
                            $mensaje = "Contraseña actualizada correctamente.";
                        } else {
                            $error_pass = "Error al guardar la contraseña. Inténtalo de nuevo.";
                        }
                    }
                } else {
                    $error_pass = "No se pudo verificar tu identidad. Inténtalo de nuevo.";
                }
            }
        }
    }
}

$res_pedidos = consultaSupabase("pedidos?usuario_id=eq.$usuario_id&order=id.desc");
$pedidos = (!empty($res_pedidos) && !isset($res_pedidos['error'])) ? $res_pedidos : [];

$detalles_map = [];
if (!empty($pedidos)) {
    $pedido_ids = array_column($pedidos, 'id');
    $ids_str = implode(',', $pedido_ids);
    $res_detalles = consultaSupabase("detalle_pedido?pedido_id=in.($ids_str)&select=*,productos(nombre)");
    if (!empty($res_detalles) && !isset($res_detalles['error'])) {
        foreach ($res_detalles as $d) {
            $detalles_map[$d['pedido_id']][] = $d;
        }
    }
}

$res_garaje = consultaSupabase("garaje_usuario?usuario_id=eq.$usuario_id");
$garaje = (!empty($res_garaje) && !isset($res_garaje['error'])) ? $res_garaje : [];
$res_coches = consultaSupabase("coches?order=marca.asc");
$coches = (!empty($res_coches) && !isset($res_coches['error'])) ? $res_coches : [];
$coches_map = [];
foreach ($coches as $c) {
    $coches_map[$c['id']] = $c;
}

if (!isset($_SESSION['coche_activo_id'])) {
    foreach ($garaje as $g) {
        if (!empty($g['seleccionado_actualmente'])) {
            $c = $coches_map[$g['coche_id']] ?? null;
            if ($c) {
                $_SESSION['coche_activo_id']     = $g['coche_id'];
                $_SESSION['coche_activo_nombre'] = $c['marca'] . ' ' . $c['modelo'];
            }
            break;
        }
    }
}

$pagina_titulo = "Panel de Cliente | AutoStock";
include_once 'includes/header.php';
?>
<main class="container py-5">
    <header class="mb-4">
        <h1 class="fw-bold titulo-oscuro"><i class="ph ph-identification-badge text-naranja me-2"></i> Mi Área de Cliente</h1>
        <p class="text-muted">Gestiona tus pedidos y tu garaje virtual desde aquí.</p>
    </header>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="ph-fill ph-<?= $tipo_mensaje === 'success' ? 'check-circle' : 'warning' ?> me-2"></i> <?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['pedido_exito'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 py-4" role="alert" style="border-left: 5px solid #198754;">
            <div class="d-flex align-items-center">
                <i class="ph-fill ph-handbag display-5 me-3 text-success"></i>
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
            <button class="nav-link text-azul-oscuro" id="dash-tab" data-bs-toggle="tab" data-bs-target="#dash" type="button" role="tab"><i class="ph ph-house me-1"></i> Resumen</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-azul-oscuro" id="garaje-tab" data-bs-toggle="tab" data-bs-target="#garaje" type="button" role="tab"><i class="ph ph-car me-1"></i> Mi Garaje</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-azul-oscuro" id="pedidos-tab" data-bs-toggle="tab" data-bs-target="#pedidos" type="button" role="tab"><i class="ph ph-package me-1"></i> Mis Pedidos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-azul-oscuro" id="perfil-tab" data-bs-toggle="tab" data-bs-target="#perfil" type="button" role="tab"><i class="ph ph-user me-1"></i> Perfil</button>
        </li>
    </ul>

    <div class="tab-content" id="clientTabsContent">

        <!-- RESUMEN -->
        <div class="tab-pane fade" id="dash" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4 text-center">
                            <i class="ph ph-car display-4 text-naranja mb-3"></i>
                            <h4 class="fw-bold">Vehículo Activo</h4>
                            <?php if (isset($_SESSION['coche_activo_nombre'])): ?>
                                <p class="text-success fw-bold fs-5 mb-1"><i class="ph-fill ph-check-circle"></i> <?= htmlspecialchars($_SESSION['coche_activo_nombre']) ?></p>
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
                            <i class="ph ph-package display-4 mb-3 text-light opacity-75"></i>
                            <h4 class="fw-bold">Tus Pedidos</h4>
                            <p class="fs-5 mb-1"><?= count($pedidos) ?> pedidos realizados</p>
                            <p class="small opacity-75">Gracias por confiar en AutoStock.</p>
                            <button class="btn btn-light text-azul-oscuro fw-bold rounded-pill mt-2" onclick="document.getElementById('pedidos-tab').click()">Ver Historial</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GARAJE -->
        <div class="tab-pane fade" id="garaje" role="tabpanel">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="fw-bold mb-3 titulo-oscuro">Vehículos Guardados</h4>
                    <?php if (empty($garaje)): ?>
                        <div class="alert alert-light border shadow-sm text-center py-5">
                            <i class="ph ph-traffic-cone display-4 text-muted mb-3"></i>
                            <h5>Tu garaje está vacío</h5>
                            <p class="text-muted mb-0">Añade tu coche para encontrar repuestos compatibles fácilmente.</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($garaje as $g):
                                $coche = $coches_map[$g['coche_id']] ?? null;
                                if (!$coche) continue;
                                $esActivo = $g['seleccionado_actualmente'];
                            ?>
                                <article class="col-md-6">
                                    <div class="card border-2 <?= $esActivo ? 'border-naranja shadow' : 'border-light shadow-sm' ?> rounded-4 h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="fw-bold mb-0"><?= htmlspecialchars($coche['marca'] . ' ' . $coche['modelo']) ?></h5>
                                                <?php if ($esActivo): ?>
                                                    <span class="badge bg-naranja"><i class="ph ph-check-circle"></i> Activo</span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-muted small mb-4">Año: <?= htmlspecialchars($coche['anio']) ?></p>
                                            <div class="d-flex gap-2">
                                                <?php if (!$esActivo): ?>
                                                    <form method="POST" class="m-0 flex-grow-1">
                                                        <?= csrfField() ?>
                                                        <input type="hidden" name="action" value="set_coche_activo">
                                                        <input type="hidden" name="garaje_id" value="<?= $g['id'] ?>">
                                                        <input type="hidden" name="coche_id" value="<?= $g['coche_id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-azul-oscuro w-100">Seleccionar</button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" class="m-0 flex-grow-1">
                                                        <?= csrfField() ?>
                                                        <input type="hidden" name="action" value="quitar_coche_activo">
                                                        <button type="submit" class="btn btn-sm btn-naranja w-100 text-white">Quitar Filtro</button>
                                                    </form>
                                                <?php endif; ?>
                                                <form method="POST" class="m-0" onsubmit="return confirm('¿Seguro que deseas quitar este coche del garaje?');">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="action" value="eliminar_coche">
                                                    <input type="hidden" name="garaje_id" value="<?= $g['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-light text-danger border"><i class="ph ph-trash"></i></button>
                                                </form>
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
                            <h5 class="fw-bold mb-3 titulo-oscuro"><i class="ph ph-plus-circle text-naranja me-2"></i> Añadir Vehículo</h5>
                            <form method="POST">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="add_coche">
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Selecciona tu coche de la base de datos:</label>
                                    <select name="coche_id" class="form-select" required>
                                        <option value="">-- Seleccionar --</option>
                                        <?php foreach ($coches as $c): ?>
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

        <!-- PEDIDOS -->
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
                                        <th>Pago</th>
                                        <th>Estado</th>
                                        <th class="text-end pe-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pedidos as $pedido):
                                        $estado = $pedido['estado'] ?? 'Pendiente';
                                        $badge_class = 'bg-secondary';
                                        if ($estado === 'Entregado') $badge_class = 'bg-success';
                                        if ($estado === 'Enviado') $badge_class = 'bg-primary';
                                        if ($estado === 'Devolución Solicitada') $badge_class = 'bg-warning text-dark';
                                        if ($estado === 'Cancelado') $badge_class = 'bg-danger';
                                        $tiene_detalles = !empty($detalles_map[$pedido['id']]);
                                    ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-muted">#<?= $pedido['id'] ?></td>
                                            <td><?= htmlspecialchars($pedido['fecha'] ?? 'N/A') ?></td>
                                            <td class="fw-bold text-naranja"><?= number_format($pedido['total'] ?? 0, 2) ?> €</td>
                                            <td class="text-muted small"><?= htmlspecialchars($pedido['forma_pago'] ?? '—') ?></td>
                                            <td><span class="badge <?= $badge_class ?>"><?= htmlspecialchars($estado) ?></span></td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex gap-2 justify-content-end align-items-center">
                                                    <?php if ($tiene_detalles): ?>
                                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#detalle-<?= $pedido['id'] ?>"
                                                            aria-expanded="false">
                                                            <i class="ph ph-list-bullets me-1"></i> Ver
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ($estado === 'Entregado'): ?>
                                                        <form method="POST" class="d-inline-block m-0" onsubmit="return confirm('¿Solicitar devolución de este pedido?');">
                                                            <?= csrfField() ?>
                                                            <input type="hidden" name="action" value="solicitar_devolucion">
                                                            <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Devolver Pedido">
                                                                <i class="ph ph-arrow-counter-clockwise"></i> Devolver
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php if ($tiene_detalles): ?>
                                            <tr class="collapse" id="detalle-<?= $pedido['id'] ?>">
                                                <td colspan="6" class="p-0">
                                                    <div class="bg-light px-4 py-3 border-top border-bottom">
                                                        <p class="small fw-bold text-muted mb-2 text-uppercase"><i class="ph ph-receipt me-1"></i> Artículos del pedido</p>
                                                        <table class="table table-sm mb-0">
                                                            <thead>
                                                                <tr class="text-muted small">
                                                                    <th>Producto</th>
                                                                    <th class="text-center">Cant.</th>
                                                                    <th class="text-end">Precio unit.</th>
                                                                    <th class="text-end">Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($detalles_map[$pedido['id']] as $det): ?>
                                                                    <tr>
                                                                        <td><?= htmlspecialchars($det['productos']['nombre'] ?? "Producto #{$det['producto_id']}") ?></td>
                                                                        <td class="text-center"><?= $det['cantidad'] ?></td>
                                                                        <td class="text-end"><?= number_format($det['precio_unitario'], 2) ?> €</td>
                                                                        <td class="text-end fw-bold"><?= number_format($det['subtotal'], 2) ?> €</td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- PERFIL -->
        <div class="tab-pane fade" id="perfil" role="tabpanel">
            <div class="row g-4" style="max-width: 900px; margin: 0 auto;">

                <!-- Datos Personales -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold titulo-oscuro mb-4">
                                <i class="ph ph-user-gear text-naranja me-2"></i> Datos Personales
                            </h5>
                            <?php if ($error_perfil): ?>
                                <div class="alert alert-danger small py-2">
                                    <i class="ph-fill ph-warning me-1"></i> <?= htmlspecialchars($error_perfil) ?>
                                </div>
                            <?php endif; ?>
                            <form method="POST">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="actualizar_perfil">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Nombre</label>
                                    <input type="text" name="nombre" class="form-control"
                                           value="<?= htmlspecialchars($_SESSION['nombre'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Apellido</label>
                                    <input type="text" name="apellido" class="form-control"
                                           value="<?= htmlspecialchars($_SESSION['apellido'] ?? '') ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold small">Email</label>
                                    <input type="email" class="form-control bg-light"
                                           value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" disabled>
                                    <div class="form-text">El email no se puede cambiar desde aquí.</div>
                                </div>
                                <button type="submit" class="btn btn-naranja w-100 fw-bold rounded-pill">
                                    <i class="ph ph-floppy-disk me-2"></i> Guardar Cambios
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Cambiar Contraseña -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold titulo-oscuro mb-4">
                                <i class="ph ph-lock-key text-naranja me-2"></i> Cambiar Contraseña
                            </h5>
                            <?php if ($error_pass): ?>
                                <div class="alert alert-danger small py-2">
                                    <i class="ph-fill ph-warning me-1"></i> <?= htmlspecialchars($error_pass) ?>
                                </div>
                            <?php endif; ?>
                            <form method="POST" id="formCambiarPass">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="cambiar_password">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Contraseña Actual</label>
                                    <div class="input-group">
                                        <input type="password" name="pass_actual" id="passActual"
                                               class="form-control" placeholder="Tu contraseña actual" required>
                                        <button type="button" class="btn btn-outline-secondary toggle-pass" data-target="passActual">
                                            <i class="ph ph-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Nueva Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" name="pass_nueva" id="passNueva"
                                               class="form-control" placeholder="Mínimo 8 caracteres" required minlength="8">
                                        <button type="button" class="btn btn-outline-secondary toggle-pass" data-target="passNueva">
                                            <i class="ph ph-eye"></i>
                                        </button>
                                    </div>
                                    <div class="mt-2 bg-light rounded overflow-hidden" style="height:4px;">
                                        <div id="pass-strength-bar" class="rounded" style="width:0%;height:4px;transition:width .3s,background .3s;"></div>
                                    </div>
                                    <small id="pass-strength-text" class="text-muted"></small>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold small">Confirmar Nueva Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" name="pass_confirmar" id="passConfirmar"
                                               class="form-control" placeholder="Repite la nueva contraseña" required>
                                        <button type="button" class="btn btn-outline-secondary toggle-pass" data-target="passConfirmar">
                                            <i class="ph ph-eye"></i>
                                        </button>
                                    </div>
                                    <small id="pass-match-msg" class="mt-1 d-none"></small>
                                </div>
                                <button type="submit" class="btn btn-azul-oscuro w-100 fw-bold rounded-pill">
                                    <i class="ph ph-lock-simple-open me-2"></i> Actualizar Contraseña
                                </button>
                            </form>
                            <div class="text-center mt-3">
                                <a href="auth/recuperar_password.php" class="small text-muted text-decoration-none">
                                    <i class="ph ph-question me-1"></i> ¿Olvidaste tu contraseña actual?
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cerrar Sesión -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 bg-light">
                        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                            <div>
                                <h6 class="fw-bold mb-1">Cerrar Sesión</h6>
                                <p class="text-muted small mb-0">Finaliza tu sesión actual en este dispositivo.</p>
                            </div>
                            <a href="auth/logout.php" class="btn btn-outline-danger rounded-pill fw-bold px-4">
                                <i class="ph ph-sign-out me-2"></i> Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</main>

<script>
(function () {
    var activeTab = <?= json_encode($active_tab) ?>;
    document.addEventListener('DOMContentLoaded', function () {
        var tabEl = document.getElementById(activeTab + '-tab');
        if (tabEl) { bootstrap.Tab.getOrCreateInstance(tabEl).show(); }

        document.querySelectorAll('.toggle-pass').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var inp = document.getElementById(this.dataset.target);
                var icon = this.querySelector('i');
                inp.type = inp.type === 'password' ? 'text' : 'password';
                icon.className = inp.type === 'password' ? 'ph ph-eye' : 'ph ph-eye-slash';
            });
        });

        var passNueva = document.getElementById('passNueva');
        var bar = document.getElementById('pass-strength-bar');
        var txt = document.getElementById('pass-strength-text');
        if (passNueva) {
            passNueva.addEventListener('input', function () {
                var v = this.value;
                var s = 0;
                if (v.length >= 8)  s++;
                if (v.length >= 12) s++;
                if (/[A-Z]/.test(v)) s++;
                if (/[0-9]/.test(v)) s++;
                if (/[^A-Za-z0-9]/.test(v)) s++;
                var cfgs = [
                    { w: '20%', c: '#e74c3c', t: 'Muy débil' },
                    { w: '40%', c: '#e67e22', t: 'Débil' },
                    { w: '60%', c: '#f1c40f', t: 'Moderada' },
                    { w: '80%', c: '#2ecc71', t: 'Fuerte' },
                    { w: '100%', c: '#27ae60', t: 'Muy fuerte' }
                ];
                var cfg = v ? (cfgs[Math.min(s - 1, 4)] || cfgs[0]) : { w: '0%', c: '#e74c3c', t: '' };
                bar.style.width = cfg.w;
                bar.style.background = cfg.c;
                txt.textContent = v ? cfg.t : '';
                txt.style.color = cfg.c;
            });
        }

        var passConf = document.getElementById('passConfirmar');
        var matchMsg = document.getElementById('pass-match-msg');
        if (passConf) {
            passConf.addEventListener('input', function () {
                var pass = document.getElementById('passNueva').value;
                matchMsg.classList.remove('d-none');
                if (this.value === pass && this.value.length > 0) {
                    matchMsg.textContent = '✓ Las contraseñas coinciden';
                    matchMsg.className = 'mt-1 text-success small';
                } else {
                    matchMsg.textContent = '✗ Las contraseñas no coinciden';
                    matchMsg.className = 'mt-1 text-danger small';
                }
            });
        }
    });
}());
</script>

<?php include_once 'includes/footer.php'; ?>
