<?php
require_once __DIR__ . '/../includes/security.php';
initSecureSession();
if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] !== true) {
    header("Location: ../index.php");
    exit;
}
require_once '../includes/supabase_api.php';
require_once '../includes/mailer.php';
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfCheck()) {
        $mensaje = "Solicitud no válida.";
    } else {
    $action = $_POST['action'] ?? '';
    if ($action === 'actualizar_estado') {
        $pedido_id = (int)($_POST['pedido_id'] ?? 0);
        $nuevo_estado = $_POST['estado'] ?? '';
        if ($pedido_id && $nuevo_estado) {
            consultaSupabase("pedidos?id=eq.$pedido_id", "PATCH", ['estado' => $nuevo_estado]);
            $mensaje = "Estado del pedido #$pedido_id actualizado a '$nuevo_estado'.";
            $res_pedido = consultaSupabase("pedidos?id=eq.$pedido_id&select=usuario_id");
            if (!empty($res_pedido) && !isset($res_pedido['error'])) {
                $uid = $res_pedido[0]['usuario_id'];
                $res_user = consultaSupabase("perfiles?id=eq.$uid&select=email,nombre");
                if (!empty($res_user) && !isset($res_user['error'])) {
                    $email_cl  = $res_user[0]['email'];
                    $nombre_cl = $res_user[0]['nombre'];
                    $cuerpo_estado = emailPlantillaCambioEstado(
                        htmlspecialchars($nombre_cl),
                        $email_cl,
                        $pedido_id,
                        $nuevo_estado
                    );
                    enviarEmail($email_cl, "Tu pedido #$pedido_id: $nuevo_estado — AutoStock", $cuerpo_estado);
                }
            }
        }
    } elseif ($action === 'actualizar_rol') {
        $usuario_id = (int)($_POST['usuario_id'] ?? 0);
        $nuevo_rol = (int)($_POST['rol_id'] ?? 2);
        if ($usuario_id) {
            consultaSupabase("perfiles?id=eq.$usuario_id", "PATCH", ['rol_id' => $nuevo_rol]);
            $mensaje = "Rol del usuario #$usuario_id actualizado.";
        }
    } elseif ($action === 'importar_csv') {
        if (isset($_FILES['archivo_csv']) && $_FILES['archivo_csv']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['archivo_csv']['tmp_name'];
            if (($handle = fopen($tmpName, 'r')) !== FALSE) {
                fgetcsv($handle, 1000, ",");
                $nuevos_productos = [];
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (count($data) >= 4) { 
                        $nuevos_productos[] = [
                            'nombre' => trim($data[0]),
                            'precio' => (float)$data[1],
                            'stock' => (int)$data[2],
                            'categoria_id' => (int)$data[3],
                            'descripcion' => trim($data[4] ?? '')
                        ];
                    }
                }
                fclose($handle);
                if (!empty($nuevos_productos)) {
                    $res = consultaSupabase("productos", "POST", $nuevos_productos);
                    if (!isset($res['error'])) {
                        $mensaje = "Se han importado " . count($nuevos_productos) . " productos correctamente vía CSV.";
                    } else {
                        $mensaje = "Error al importar: " . $res['error'];
                    }
                }
            }
        }
    } elseif ($action === 'marcar_leido') {
        $msg_id = $_POST['mensaje_id'] ?? '';
        if ($msg_id !== '') {
            consultaSupabase("mensajes_contacto?id=eq.$msg_id", "PATCH", ['leido' => true]);
            $file_path = __DIR__ . '/../data/mensajes_contacto.json';
            if (file_exists($file_path)) {
                $content = file_get_contents($file_path);
                $mensajes = json_decode($content, true) ?: [];
                foreach ($mensajes as &$m) {
                    if ($m['id'] == $msg_id) {
                        $m['leido'] = true;
                        break;
                    }
                }
                file_put_contents($file_path, json_encode($mensajes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
            $mensaje = "Mensaje marcado como leído.";
        }
    } elseif ($action === 'eliminar_mensaje') {
        $msg_id = $_POST['mensaje_id'] ?? '';
        if ($msg_id !== '') {
            consultaSupabase("mensajes_contacto?id=eq.$msg_id", "DELETE");
            $file_path = __DIR__ . '/../data/mensajes_contacto.json';
            if (file_exists($file_path)) {
                $content = file_get_contents($file_path);
                $mensajes = json_decode($content, true) ?: [];
                $mensajes = array_filter($mensajes, function($m) use ($msg_id) {
                    return $m['id'] != $msg_id;
                });
                file_put_contents($file_path, json_encode(array_values($mensajes), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
            $mensaje = "Mensaje de contacto eliminado.";
        }
    }
    }
}
$res_productos = consultaSupabase("productos?select=*&order=id.desc");
$productos = (!empty($res_productos) && !isset($res_productos['error'])) ? $res_productos : [];
$res_pedidos = consultaSupabase("pedidos?select=*&order=id.desc");
$pedidos = (!empty($res_pedidos) && !isset($res_pedidos['error'])) ? $res_pedidos : [];
$res_usuarios = consultaSupabase("perfiles?select=*&order=id.desc");
$usuarios = (!empty($res_usuarios) && !isset($res_usuarios['error'])) ? $res_usuarios : [];
$res_mensajes = consultaSupabase("mensajes_contacto?select=*&order=fecha.desc");
$mensajes_contacto = [];
if (!empty($res_mensajes) && !isset($res_mensajes['error']) && is_array($res_mensajes)) {
    $mensajes_contacto = $res_mensajes;
}
$file_path = __DIR__ . '/../data/mensajes_contacto.json';
if (file_exists($file_path)) {
    $content = file_get_contents($file_path);
    $mensajes_locales = json_decode($content, true) ?: [];
    if (empty($mensajes_contacto)) {
        $mensajes_contacto = $mensajes_locales;
    } else {
        $fechas_existentes = array_column($mensajes_contacto, 'fecha');
        foreach ($mensajes_locales as $ml) {
            if (!in_array($ml['fecha'], $fechas_existentes)) {
                $mensajes_contacto[] = $ml;
            }
        }
    }
}
usort($mensajes_contacto, function($a, $b) {
    return strcmp($b['fecha'] ?? '', $a['fecha'] ?? '');
});
$total_ingresos = array_sum(array_column($pedidos, 'total'));
$total_pedidos = count($pedidos);
$total_clientes = count($usuarios);
$stock_bajo = count(array_filter($productos, function($p) { return $p['stock'] <= 5; }));
$grafica_ingresos = [];
$grafica_pedidos = [];
foreach ($pedidos as $p) {
    if (isset($p['estado']) && $p['estado'] === 'Cancelado') continue;
    $fecha = substr($p['fecha'] ?? date('Y-m-d'), 0, 10);
    if (!isset($grafica_ingresos[$fecha])) {
        $grafica_ingresos[$fecha] = 0;
        $grafica_pedidos[$fecha] = 0;
    }
    $grafica_ingresos[$fecha] += (float)($p['total'] ?? 0);
    $grafica_pedidos[$fecha] += 1;
}
ksort($grafica_ingresos);
ksort($grafica_pedidos);
$fechas_labels = json_encode(array_keys($grafica_ingresos));
$datos_ingresos = json_encode(array_values($grafica_ingresos));
$datos_pedidos = json_encode(array_values($grafica_pedidos));
$mensajes_pendientes = count(array_filter($mensajes_contacto, function($m) { return !($m['leido'] ?? false); }));
if (isset($_SESSION['admin_msg'])) {
    $mensaje_flash = $_SESSION['admin_msg'];
    unset($_SESSION['admin_msg']);
} else {
    $mensaje_flash = null;
}
$pagina_titulo = "Panel Avanzado | AutoStock";
include_once '../includes/header.php';
?>
<script src="../assets/js/admin-tabs.js"></script>
<main class="container py-5">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold titulo-oscuro"><i class="ph-fill ph-shield-check text-naranja me-2"></i> Admin Panel</h1>
        <a href="crear.php" class="btn btn-naranja fw-bold"><i class="ph ph-plus me-2"></i> Añadir Producto</a>
    </header>
    <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="ph-fill ph-check-circle me-2"></i> <?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($mensaje_flash): ?>
        <div class="alert alert-<?= $mensaje_flash['tipo'] ?> alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="<?= $mensaje_flash['tipo'] === 'success' ? 'ph-fill ph-check-circle' : 'ph-fill ph-warning' ?> me-2"></i>
            <?= htmlspecialchars($mensaje_flash['texto']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <ul class="nav nav-tabs nav-fill mb-4 fw-bold" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active text-azul-oscuro" data-tab="dash" type="button" onclick="adminTab('dash')"><i class="ph ph-gauge me-1"></i> Dashboard</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-azul-oscuro" data-tab="pedidos" type="button" onclick="adminTab('pedidos')"><i class="ph ph-package me-1"></i> Pedidos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-azul-oscuro" data-tab="productos" type="button" onclick="adminTab('productos')"><i class="ph ph-tag me-1"></i> Catálogo</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-azul-oscuro" data-tab="clientes" type="button" onclick="adminTab('clientes')"><i class="ph ph-users me-1"></i> Clientes</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-azul-oscuro" data-tab="mensajes" type="button" onclick="adminTab('mensajes')">
                <i class="ph-fill ph-envelope-simple me-1"></i> Mensajes
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-azul-oscuro" data-tab="herramientas" type="button" onclick="adminTab('herramientas')"><i class="ph ph-wrench me-1"></i> Herramientas</button>
        </li>
    </ul>
    <div class="tab-content" id="adminTabsContent">
        <div class="tab-pane active" id="dash" role="tabpanel" style="display:block">
            <div class="row g-4 mb-4">
                <article class="col-md-3">
                    <div class="card shadow-sm border-0 text-center py-4 rounded-4 bg-white stat-card">
                        <i class="ph ph-coins display-4 text-success mb-2"></i>
                        <h3 class="fw-bold mb-0" data-counter data-target="<?= $total_ingresos ?>"><?= number_format($total_ingresos, 2) ?> €</h3>
                        <p class="text-muted mb-0 small">Ingresos Totales</p>
                    </div>
                </article>
                <article class="col-md-3">
                    <div class="card shadow-sm border-0 text-center py-4 rounded-4 bg-white stat-card">
                        <i class="ph ph-package display-4 text-naranja mb-2"></i>
                        <h3 class="fw-bold mb-0" data-counter data-target="<?= $total_pedidos ?>"><?= $total_pedidos ?></h3>
                        <p class="text-muted mb-0 small">Pedidos Realizados</p>
                    </div>
                </article>
                <article class="col-md-3">
                    <div class="card shadow-sm border-0 text-center py-4 rounded-4 bg-white stat-card">
                        <i class="ph ph-users display-4 text-azul-oscuro mb-2"></i>
                        <h3 class="fw-bold mb-0" data-counter data-target="<?= $total_clientes ?>"><?= $total_clientes ?></h3>
                        <p class="text-muted mb-0 small">Clientes Registrados</p>
                    </div>
                </article>
                <article class="col-md-3">
                    <div class="card shadow-sm border-0 text-center py-4 rounded-4 bg-white stat-card <?= $stock_bajo > 0 ? 'border border-danger border-2' : '' ?>">
                        <i class="ph ph-warning display-4 <?= $stock_bajo > 0 ? 'text-danger' : 'text-secondary' ?> mb-2"></i>
                        <h3 class="fw-bold mb-0 <?= $stock_bajo > 0 ? 'text-danger' : '' ?>" data-counter data-target="<?= $stock_bajo ?>"><?= $stock_bajo ?></h3>
                        <p class="text-muted mb-0 small">Alertas Stock Bajo</p>
                    </div>
                </article>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold titulo-oscuro mb-3"><i class="ph ph-trend-up me-2 text-naranja"></i>Evolución de Ingresos</h5>
                            <canvas id="graficaIngresos" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold titulo-oscuro mb-3"><i class="ph-fill ph-chart-bar me-2 text-naranja"></i>Volumen de Pedidos</h5>
                            <canvas id="graficaPedidos" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const labels = <?= $fechas_labels ?>;
                    new Chart(document.getElementById('graficaIngresos'), {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Ingresos (€)',
                                data: <?= $datos_ingresos ?>,
                                borderColor: '#192C76',
                                backgroundColor: 'rgba(25, 44, 118, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                    new Chart(document.getElementById('graficaPedidos'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Pedidos',
                                data: <?= $datos_pedidos ?>,
                                backgroundColor: '#FF7403',
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                        }
                    });
                });
            </script>
            <div class="alert alert-info border-0 shadow-sm rounded-4">
                <i class="ph-fill ph-info me-2"></i> <strong>Tip:</strong> Navega por las pestañas superiores para gestionar el inventario, clientes, mensajes y herramientas de importación masiva.
            </div>
        </div>
        <div class="tab-pane" id="pedidos" role="tabpanel" style="display:none">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <?php if (empty($pedidos)): ?>
                        <p class="text-muted text-center py-5 mb-0">No hay pedidos registrados.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">ID</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pedidos as $pedido): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold">#<?= $pedido['id'] ?></td>
                                            <td><?= htmlspecialchars($pedido['fecha'] ?? 'N/A') ?></td>
                                            <td class="fw-bold text-naranja"><?= number_format($pedido['total'] ?? 0, 2) ?> €</td>
                                            <td>
                                                <form method="POST" class="d-flex align-items-center gap-2 m-0">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="action" value="actualizar_estado">
                                                    <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                                                    <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 140px;">
                                                        <option value="Pendiente" <?= ($pedido['estado']??'') === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                                        <option value="En Preparación" <?= ($pedido['estado']??'') === 'En Preparación' ? 'selected' : '' ?>>En Preparación</option>
                                                        <option value="Enviado" <?= ($pedido['estado']??'') === 'Enviado' ? 'selected' : '' ?>>Enviado</option>
                                                        <option value="Entregado" <?= ($pedido['estado']??'') === 'Entregado' ? 'selected' : '' ?>>Entregado</option>
                                                        <option value="Cancelado" <?= ($pedido['estado']??'') === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                                    </select>
                                                </form>
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
        <div class="tab-pane" id="productos" role="tabpanel" style="display:none">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <?php if (empty($productos)): ?>
                        <p class="text-muted text-center py-5 mb-0">No hay productos en el catálogo.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Ref.</th>
                                        <th>Nombre del Repuesto</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th class="text-end pe-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productos as $p): 
                                        $esBajoStock = $p['stock'] <= 5;
                                    ?>
                                        <tr class="<?= $esBajoStock ? 'table-danger' : '' ?>">
                                            <td class="ps-4 text-muted small">#<?= $p['id'] ?></td>
                                            <td class="fw-bold"><?= htmlspecialchars($p['nombre']) ?></td>
                                            <td class="fw-bold text-naranja"><?= number_format($p['precio'], 2) ?> €</td>
                                            <td>
                                                <span class="badge <?= $esBajoStock ? 'bg-danger' : 'bg-success' ?>">
                                                    <?= $p['stock'] ?> uds
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="editar_producto.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-naranja me-1" title="Editar">
                                                    <i class="ph ph-note-pencil"></i>
                                                </a>
                                                <form method="POST" action="eliminar_producto.php" class="d-inline-block m-0" onsubmit="return confirm('¿Seguro que deseas eliminar este repuesto?');">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                        <i class="ph ph-trash"></i>
                                                    </button>
                                                </form>
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
        <div class="tab-pane" id="clientes" role="tabpanel" style="display:none">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $u): ?>
                                    <tr>
                                        <td class="ps-4 text-muted">#<?= $u['id'] ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($u['nombre'] . ' ' . ($u['apellido']??'')) ?></td>
                                        <td><a href="mailto:<?= htmlspecialchars($u['email']) ?>"><?= htmlspecialchars($u['email']) ?></a></td>
                                        <td>
                                            <form method="POST" class="m-0">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="action" value="actualizar_rol">
                                                <input type="hidden" name="usuario_id" value="<?= $u['id'] ?>">
                                                <select name="rol_id" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                    <option value="1" <?= $u['rol_id'] == 1 ? 'selected' : '' ?>>Admin</option>
                                                    <option value="2" <?= $u['rol_id'] == 2 ? 'selected' : '' ?>>Cliente (B2C)</option>
                                                    <option value="3" <?= $u['rol_id'] == 3 ? 'selected' : '' ?>>Taller (B2B)</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="herramientas" role="tabpanel" style="display:none">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4">
                            <h4 class="fw-bold titulo-oscuro mb-3"><i class="ph ph-file-csv text-naranja me-2"></i> Importación Masiva (CSV)</h4>
                            <p class="text-muted small mb-4">Sube un archivo CSV para cargar cientos de productos a la vez. <br><strong>Formato:</strong> Nombre, Precio, Stock, Categoria_ID, Descripcion</p>
                            <form method="POST" enctype="multipart/form-data">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="importar_csv">
                                <div class="mb-3">
                                    <input type="file" name="archivo_csv" class="form-control" accept=".csv" required>
                                </div>
                                <button type="submit" class="btn btn-naranja w-100 fw-bold"><i class="ph ph-cloud-arrow-up me-2"></i> Iniciar Importación</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="mensajes" role="tabpanel" style="display:none">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <?php if (empty($mensajes_contacto)): ?>
                        <div class="text-center py-5">
                            <i class="ph ph-envelope-open display-3 text-muted opacity-50 mb-3"></i>
                            <h5 class="fw-bold text-azul-oscuro">No hay mensajes de contacto</h5>
                            <p class="text-muted mb-0">Cuando los usuarios envíen el formulario de contacto, aparecerán aquí.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 py-3">Fecha</th>
                                        <th class="py-3">Remitente</th>
                                        <th class="py-3">Asunto</th>
                                        <th class="py-3">Estado</th>
                                        <th class="text-center pe-4 py-3" style="width: 180px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($mensajes_contacto as $m): 
                                        $esLeido = $m['leido'] ?? false;
                                        $idMsg = $m['id'];
                                    ?>
                                        <tr class="<?= !$esLeido ? 'table-warning' : '' ?>" style="transition: background-color 0.2s;">
                                            <td class="ps-4 text-muted small"><?= htmlspecialchars($m['fecha'] ?? 'N/A') ?></td>
                                            <td>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($m['nombre'] ?? 'Invitado') ?></div>
                                                <a href="mailto:<?= htmlspecialchars($m['email'] ?? '') ?>" class="small text-decoration-none text-muted"><i class="ph ph-envelope-simple me-1"></i><?= htmlspecialchars($m['email'] ?? '') ?></a>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border me-2"><?= htmlspecialchars($m['asunto'] ?? 'General') ?></span>
                                                <span class="text-clamp text-muted small" style="display: inline-block; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: bottom;">
                                                    <?= htmlspecialchars($m['mensaje'] ?? '') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!$esLeido): ?>
                                                    <span class="badge bg-naranja text-white">No leído</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Leído</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center pe-4">
                                                <div class="d-inline-flex gap-1">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalMensaje<?= $idMsg ?>" title="Leer mensaje completo">
                                                        <i class="ph-fill ph-eye"></i> Leer
                                                    </button>
                                                    <?php if (!$esLeido): ?>
                                                        <form method="POST" class="d-inline-block m-0">
                                                            <?= csrfField() ?>
                                                            <input type="hidden" name="action" value="marcar_leido">
                                                            <input type="hidden" name="mensaje_id" value="<?= $idMsg ?>">
                                                            <button type="submit" class="btn btn-sm btn-success text-white" title="Marcar como leído">
                                                                <i class="ph ph-check"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <form method="POST" class="d-inline-block m-0" onsubmit="return confirm('¿Seguro que deseas eliminar este mensaje de contacto?');">
                                                        <?= csrfField() ?>
                                                        <input type="hidden" name="action" value="eliminar_mensaje">
                                                        <input type="hidden" name="mensaje_id" value="<?= $idMsg ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar mensaje">
                                                            <i class="ph-fill ph-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php foreach ($mensajes_contacto as $m): 
                            $esLeido = $m['leido'] ?? false;
                            $idMsg = $m['id'];
                        ?>
                            <div class="modal fade" id="modalMensaje<?= $idMsg ?>" tabindex="-1" aria-labelledby="modalLabelMsg<?= $idMsg ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header bg-azul-oscuro text-white border-0 pb-3" style="border-radius: 15px 15px 0 0;">
                                            <h5 class="modal-title fw-bold" id="modalLabelMsg<?= $idMsg ?>"><i class="ph ph-envelope-open me-2"></i> Mensaje de <?= htmlspecialchars($m['nombre'] ?? 'Invitado') ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 text-start">
                                            <div class="mb-3">
                                                <label class="fw-bold text-muted small d-block">Asunto</label>
                                                <span class="badge bg-naranja text-white fs-6"><?= htmlspecialchars($m['asunto'] ?? 'General') ?></span>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fw-bold text-muted small d-block">Remitente</label>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($m['nombre'] ?? 'Invitado') ?></div>
                                                <a href="mailto:<?= htmlspecialchars($m['email'] ?? '') ?>" class="text-decoration-none text-primary"><i class="ph ph-envelope-simple me-1"></i><?= htmlspecialchars($m['email'] ?? '') ?></a>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fw-bold text-muted small d-block">Fecha de Recepción</label>
                                                <div class="text-muted"><i class="ph ph-calendar-blank me-1"></i> <?= htmlspecialchars($m['fecha'] ?? 'N/A') ?></div>
                                            </div>
                                            <hr>
                                            <div class="mb-3">
                                                <label class="fw-bold text-muted small d-block mb-1">Cuerpo del Mensaje</label>
                                                <div class="bg-light p-3 rounded border text-dark fs-6" style="white-space: pre-wrap; font-family: inherit; max-height: 250px; overflow-y: auto;">
                                                    <?= htmlspecialchars($m['mensaje'] ?? 'Sin contenido.') ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
                                            <form method="POST" class="m-0" onsubmit="return confirm('¿Seguro que deseas eliminar este mensaje?');">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="action" value="eliminar_mensaje">
                                                <input type="hidden" name="mensaje_id" value="<?= $idMsg ?>">
                                                <button type="submit" class="btn btn-outline-danger rounded-pill fw-bold px-3" data-bs-dismiss="modal"><i class="ph ph-trash"></i> Eliminar</button>
                                            </form>
                                            <div class="d-flex gap-2">
                                                <?php if (!$esLeido): ?>
                                                    <form method="POST" class="m-0">
                                                        <?= csrfField() ?>
                                                        <input type="hidden" name="action" value="marcar_leido">
                                                        <input type="hidden" name="mensaje_id" value="<?= $idMsg ?>">
                                                        <button type="submit" class="btn btn-success text-white rounded-pill fw-bold px-3" data-bs-dismiss="modal"><i class="ph ph-check"></i> Marcar como Leído</button>
                                                    </form>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-secondary rounded-pill fw-bold px-3" data-bs-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include_once '../includes/footer.php'; ?>
