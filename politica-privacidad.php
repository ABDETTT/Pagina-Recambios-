<?php
$pagina_titulo = 'Política de Privacidad | AutoStock';
require_once 'includes/header.php';
?>
<main class="container py-5" style="max-width: 860px;">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none" style="color:var(--azul-oscuro);">Inicio</a></li>
            <li class="breadcrumb-item active">Política de Privacidad</li>
        </ol>
    </nav>
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        <div class="mb-4 pb-3 border-bottom">
            <h1 class="fw-bold titulo-oscuro fs-3">Política de Privacidad</h1>
            <p class="text-muted small mb-0">Última actualización: <?= date('d/m/Y') ?> &bull; Versión 1.0</p>
        </div>
        <div class="legal-content">
            <h2 class="fs-5 fw-bold titulo-oscuro mt-4">1. Responsable del tratamiento</h2>
            <p>En cumplimiento del Reglamento (UE) 2016/679 del Parlamento Europeo y del Consejo (RGPD) y de la Ley Orgánica 3/2018, de 5 de diciembre, de Protección de Datos Personales y garantía de los derechos digitales (LOPDGDD), le informamos que los datos personales recabados a través de este sitio web son tratados por:</p>
            <div class="info-box p-3 rounded-3 mb-3" style="background:#f0f4ff; border-left: 4px solid var(--azul-oscuro);">
                <ul class="list-unstyled mb-0 small">
                    <li><strong>Razón social:</strong> AutoStock, S.L.</li>
                    <li><strong>CIF:</strong> B-12345678</li>
                    <li><strong>Dirección:</strong> Calle Mayor 42, 28001 Madrid, España</li>
                    <li><strong>Email:</strong> privacidad@autostock.com</li>
                    <li><strong>Teléfono:</strong> +34 91 000 0000</li>
                </ul>
            </div>
            <h2 class="fs-5 fw-bold titulo-oscuro mt-4">2. Finalidades y base legal del tratamiento</h2>
            <p>Tratamos sus datos personales con las siguientes finalidades y bases legales:</p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>Finalidad</th>
                            <th>Base legal</th>
                            <th>Plazo de conservación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Gestión de cuenta de usuario (registro y acceso)</td>
                            <td>Ejecución del contrato (art. 6.1.b RGPD)</td>
                            <td>Mientras la cuenta permanezca activa + 3 años</td>
                        </tr>
                        <tr>
                            <td>Gestión y tramitación de pedidos</td>
                            <td>Ejecución del contrato (art. 6.1.b RGPD)</td>
                            <td>5 años (obligación fiscal)</td>
                        </tr>
                        <tr>
                            <td>Envío de comunicaciones transaccionales (confirmación de pedido, estado del envío)</td>
                            <td>Ejecución del contrato (art. 6.1.b RGPD)</td>
                            <td>Duración del contrato</td>
                        </tr>
                        <tr>
                            <td>Envío de newsletter y comunicaciones comerciales</td>
                            <td>Consentimiento (art. 6.1.a RGPD)</td>
                            <td>Hasta retirada del consentimiento</td>
                        </tr>
                        <tr>
                            <td>Atención al cliente y gestión de consultas</td>
                            <td>Interés legítimo (art. 6.1.f RGPD)</td>
                            <td>2 años desde la última interacción</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h2 class="fs-5 fw-bold titulo-oscuro mt-4">3. Categorías de datos tratados</h2>
            <p>Tratamos las siguientes categorías de datos personales:</p>
            <ul>
                <li><strong>Datos identificativos:</strong> nombre, apellidos, dirección de email.</li>
                <li><strong>Datos de contacto:</strong> dirección de entrega, número de teléfono (si se facilita).</li>
                <li><strong>Datos económicos:</strong> historial de pedidos y facturas (no almacenamos datos de tarjetas de crédito).</li>
                <li><strong>Datos de navegación:</strong> dirección IP, cookies técnicas y de análisis (ver Política de Cookies).</li>
                <li><strong>Datos del vehículo:</strong> marca y modelo de los vehículos añadidos al garaje virtual del usuario.</li>
            </ul>
            <h2 class="fs-5 fw-bold titulo-oscuro mt-4">4. Destinatarios y encargados de tratamiento</h2>
            <p>No cedemos sus datos a terceros salvo obligación legal. Sus datos pueden ser comunicados a los siguientes encargados de tratamiento bajo las garantías del RGPD:</p>
            <ul>
                <li><strong>Supabase Inc.</strong> (proveedor de base de datos en la nube) — con domicilio en EE.UU., amparado por cláusulas contractuales tipo de la Comisión Europea.</li>
                <li><strong>Proveedores de transporte</strong> (para la entrega de pedidos), únicamente los datos necesarios para la entrega.</li>
                <li><strong>Pasarelas de pago</strong> (para la tramitación de cobros), bajo sus propias políticas de privacidad.</li>
            </ul>
            <h2 class="fs-5 fw-bold titulo-oscuro mt-4">5. Sus derechos</h2>
            <p>En cualquier momento puede ejercer los siguientes derechos ante el responsable del tratamiento:</p>
            <div class="row g-3 my-1">
                <?php
                $derechos = [
                    ['🔍', 'Acceso', 'Conocer qué datos tenemos sobre usted.'],
                    ['✏️', 'Rectificación', 'Corregir datos inexactos o incompletos.'],
                    ['🗑️', 'Supresión', 'Solicitar el borrado de sus datos ("derecho al olvido").'],
                    ['⏸️', 'Limitación', 'Restringir el tratamiento en determinadas circunstancias.'],
                    ['📦', 'Portabilidad', 'Recibir sus datos en formato estructurado y legible.'],
                    ['🚫', 'Oposición', 'Oponerse al tratamiento basado en interés legítimo.'],
                ];
                foreach ($derechos as $d):
                ?>
                <div class="col-md-4">
                    <div class="p-3 rounded-3 h-100" style="background:#f8f9fa;">
                        <div class="fs-4 mb-1"><?= $d[0] ?></div>
                        <strong class="small"><?= $d[1] ?></strong>
                        <p class="small text-muted mb-0 mt-1"><?= $d[2] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <p class="mt-3">Para ejercer sus derechos, envíe un correo a <a href="mailto:privacidad@autostock.com" style="color:var(--azul-oscuro);">privacidad@autostock.com</a> con copia de su DNI o documento identificativo equivalente. Responderemos en un plazo máximo de <strong>30 días</strong>.</p>
            <p>Si considera que el tratamiento no es conforme al RGPD, tiene derecho a presentar una reclamación ante la <strong>Agencia Española de Protección de Datos (AEPD)</strong> — <a href="https://www.aepd.es" target="_blank" rel="noopener" style="color:var(--azul-oscuro);">www.aepd.es</a>.</p>
            <h2 class="fs-5 fw-bold titulo-oscuro mt-4">6. Seguridad</h2>
            <p>Aplicamos medidas técnicas y organizativas apropiadas para proteger sus datos frente a accesos no autorizados, pérdida o destrucción, incluyendo cifrado en tránsito (HTTPS/TLS), hashing de contraseñas con <code>bcrypt</code> y control de acceso por roles.</p>
            <h2 class="fs-5 fw-bold titulo-oscuro mt-4">7. Política de Cookies</h2>
            <p>Utilizamos cookies propias estrictamente necesarias para el funcionamiento del sitio (sesión de usuario, carrito de compra) y cookies de terceros para análisis de tráfico. Consulte nuestra <a href="aviso-legal.php#cookies" style="color:var(--azul-oscuro);">Política de Cookies</a> para más información.</p>
            <h2 class="fs-5 fw-bold titulo-oscuro mt-4">8. Modificaciones</h2>
            <p>Podemos actualizar esta política en cualquier momento. En caso de cambios significativos, se lo notificaremos por email o mediante un aviso destacado en el sitio web.</p>
        </div>
        <div class="mt-5 pt-4 border-top text-center">
            <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 me-2">
                <i class="bi bi-arrow-left me-1"></i> Volver al inicio
            </a>
            <a href="aviso-legal.php" class="btn rounded-pill px-4" style="background:var(--azul-oscuro);color:#fff;">
                Aviso Legal <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</main>
<?php require_once 'includes/footer.php'; ?>
