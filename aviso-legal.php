<?php
$pagina_titulo = 'Aviso Legal y Cookies | AutoStock';
require_once 'includes/header.php';
?>
<main class="container py-5" style="max-width: 860px;">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none" style="color:var(--azul-oscuro);">Inicio</a></li>
            <li class="breadcrumb-item active">Aviso Legal y Cookies</li>
        </ol>
    </nav>
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background:#f0f4ff;">
        <h6 class="fw-bold titulo-oscuro mb-3"><i class="ph ph-list me-2"></i>Contenido de esta página</h6>
        <div class="row g-2 small">
            <div class="col-md-6">
                <a href="#aviso" class="text-decoration-none d-block py-1" style="color:var(--azul-oscuro);">→ Aviso Legal</a>
                <a href="#condiciones" class="text-decoration-none d-block py-1" style="color:var(--azul-oscuro);">→ Condiciones de Uso</a>
                <a href="#propiedad-intelectual" class="text-decoration-none d-block py-1" style="color:var(--azul-oscuro);">→ Propiedad Intelectual</a>
            </div>
            <div class="col-md-6">
                <a href="#cookies" class="text-decoration-none d-block py-1" style="color:var(--azul-oscuro);">→ Política de Cookies</a>
                <a href="#responsabilidad" class="text-decoration-none d-block py-1" style="color:var(--azul-oscuro);">→ Limitación de Responsabilidad</a>
                <a href="#legislacion" class="text-decoration-none d-block py-1" style="color:var(--azul-oscuro);">→ Legislación Aplicable</a>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        <div class="mb-4 pb-3 border-bottom">
            <h1 class="fw-bold titulo-oscuro fs-3">Aviso Legal y Política de Cookies</h1>
            <p class="text-muted small mb-0">Última actualización: <?= date('d/m/Y') ?> &bull; Versión 1.0</p>
        </div>
        <section id="aviso">
            <h2 class="fs-5 fw-bold titulo-oscuro mt-2">1. Identificación del titular</h2>
            <p>En cumplimiento del artículo 10 de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico (LSSI-CE), le informamos que este sitio web es titularidad de:</p>
            <div class="p-3 rounded-3 mb-3" style="background:#f0f4ff; border-left: 4px solid var(--azul-oscuro);">
                <ul class="list-unstyled mb-0 small">
                    <li><strong>Denominación social:</strong> AutoStock, S.L.</li>
                    <li><strong>CIF:</strong> B-12345678</li>
                    <li><strong>Domicilio social:</strong> Calle Mayor 42, 28001 Madrid, España</li>
                    <li><strong>Registro Mercantil:</strong> Registro Mercantil de Madrid, Tomo 00000, Folio 000, Hoja M-000000</li>
                    <li><strong>Email:</strong> info@autostock.com</li>
                    <li><strong>Teléfono:</strong> +34 91 000 0000</li>
                    <li><strong>Dominio web:</strong> www.autostock.com</li>
                </ul>
            </div>
        </section>
        <section id="condiciones">
            <h2 class="fs-5 fw-bold titulo-oscuro mt-4">2. Condiciones de uso</h2>
            <p>El acceso y uso de este sitio web implica la aceptación de las presentes condiciones. El usuario se compromete a hacer un uso correcto del sitio web y de los servicios que se ofrecen, de conformidad con la ley, las buenas costumbres y el orden público.</p>
            <p>Queda expresamente prohibido:</p>
            <ul>
                <li>El uso del sitio web con fines fraudulentos o que sean contrarios a la ley.</li>
                <li>La realización de pedidos con datos de identidad, dirección o pago falsos.</li>
                <li>Cualquier actuación que pueda dañar, inutilizar o deteriorar los sistemas de información del sitio.</li>
                <li>La extracción masiva de datos o contenidos del catálogo mediante medios automatizados (<em>scraping</em>).</li>
            </ul>
            <h3 class="fs-6 fw-bold titulo-oscuro mt-3">2.1. Menores de edad</h3>
            <p>El uso de este sitio web está dirigido a personas mayores de 18 años. Los menores de edad deben contar con el consentimiento de sus padres o tutores legales para realizar compras.</p>
            <h3 class="fs-6 fw-bold titulo-oscuro mt-3">2.2. Proceso de compra</h3>
            <p>La formalización de un pedido supone la aceptación plena de las condiciones generales de venta vigentes en el momento del pedido. Los precios publicados incluyen el IVA vigente (21% con carácter general). AutoStock se reserva el derecho a modificar los precios sin previo aviso.</p>
            <p>Las órdenes de compra quedan sujetas a la disponibilidad de stock. En caso de indisponibilidad sobrevenida, se le notificará en el plazo máximo de 48 horas.</p>
            <h3 class="fs-6 fw-bold titulo-oscuro mt-3">2.3. Devoluciones y desistimiento</h3>
            <p>De acuerdo con el Real Decreto Legislativo 1/2007, de 16 de noviembre, por el que se aprueba el texto refundido de la Ley General para la Defensa de los Consumidores y Usuarios, el cliente dispone de <strong>14 días naturales</strong> desde la recepción del pedido para ejercer el derecho de desistimiento sin necesidad de indicar el motivo.</p>
            <p>Quedan excluidos del derecho de desistimiento los artículos personalizados o que por razones de higiene no puedan ser devueltos una vez abiertos.</p>
        </section>
        <section id="propiedad-intelectual">
            <h2 class="fs-5 fw-bold titulo-oscuro mt-4">3. Propiedad intelectual e industrial</h2>
            <p>Todos los contenidos del sitio web (textos, imágenes, logotipos, marcas, diseño gráfico, código fuente) son propiedad de AutoStock, S.L. o de terceros que han autorizado su uso, y están protegidos por la legislación española e internacional de propiedad intelectual e industrial.</p>
            <p>Queda prohibida la reproducción total o parcial, distribución, modificación, comunicación pública o cualquier otra forma de explotación sin la autorización expresa y por escrito del titular.</p>
        </section>
        <section id="cookies">
            <h2 class="fs-5 fw-bold titulo-oscuro mt-4">4. Política de Cookies</h2>
            <p>Este sitio web utiliza cookies propias y de terceros. A continuación encontrará información detallada sobre qué son, para qué sirven y cómo puede gestionarlas.</p>
            <h3 class="fs-6 fw-bold titulo-oscuro mt-3">4.1. ¿Qué son las cookies?</h3>
            <p>Las cookies son pequeños archivos de texto que se almacenan en su dispositivo cuando visita un sitio web. Permiten recordar información sobre su visita para facilitar su experiencia y analizar el uso del sitio.</p>
            <h3 class="fs-6 fw-bold titulo-oscuro mt-3">4.2. Cookies que utilizamos</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Finalidad</th>
                            <th>Duración</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>PHPSESSID</code></td>
                            <td><span class="badge bg-success">Técnica / Necesaria</span></td>
                            <td>Mantiene la sesión del usuario (carrito, login). Estrictamente necesaria.</td>
                            <td>Sesión</td>
                        </tr>
                        <tr>
                            <td><code>cookies_accepted</code></td>
                            <td><span class="badge bg-success">Técnica / Necesaria</span></td>
                            <td>Recuerda si el usuario ha aceptado el aviso de cookies.</td>
                            <td>1 año</td>
                        </tr>
                        <tr>
                            <td><code>localStorage: cookies-accepted</code></td>
                            <td><span class="badge bg-success">Técnica / Necesaria</span></td>
                            <td>Almacenamiento local para gestionar el banner de cookies.</td>
                            <td>Persistente</td>
                        </tr>
                        <tr>
                            <td><code>_ga, _gid</code></td>
                            <td><span class="badge bg-warning text-dark">Analítica</span></td>
                            <td>Google Analytics: mide el tráfico y el comportamiento de los usuarios de forma anónima.</td>
                            <td>2 años / 24h</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h3 class="fs-6 fw-bold titulo-oscuro mt-3">4.3. ¿Cómo gestionar las cookies?</h3>
            <p>Puede configurar su navegador para rechazar o eliminar las cookies. Tenga en cuenta que deshabilitar las cookies técnicas puede afectar al funcionamiento del sitio (p.ej., el carrito de la compra o el inicio de sesión).</p>
            <div class="row g-2 mt-1">
                <?php
                $navegadores = [
                    ['Chrome', 'https://support.google.com/chrome/answer/95647', 'bi-browser-chrome'],
                    ['Firefox', 'https://support.mozilla.org/es/kb/habilitar-y-deshabilitar-cookies', 'bi-browser-firefox'],
                    ['Safari', 'https://support.apple.com/es-es/guide/safari/sfri11471/mac', 'bi-apple'],
                    ['Edge', 'https://support.microsoft.com/es-es/microsoft-edge/eliminar-las-cookies-en-microsoft-edge', 'bi-browser-edge'],
                ];
                foreach ($navegadores as $nav):
                ?>
                <div class="col-6 col-md-3">
                    <a href="<?= $nav[1] ?>" target="_blank" rel="noopener"
                       class="btn btn-outline-secondary w-100 rounded-3 small d-flex align-items-center justify-content-center gap-2">
                        <i class="bi <?= $nav[2] ?>"></i> <?= $nav[0] ?>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <section id="responsabilidad">
            <h2 class="fs-5 fw-bold titulo-oscuro mt-4">5. Limitación de responsabilidad</h2>
            <p>AutoStock no se hace responsable de los daños o perjuicios que pudieran derivarse del uso indebido del sitio web o de la imposibilidad de acceso al mismo por causas ajenas a su voluntad (fuerza mayor, fallos de terceros, etc.).</p>
            <p>Las fichas de productos incluyen información técnica facilitada por los fabricantes. AutoStock no garantiza la exactitud absoluta de dicha información y recomienda contrastarla con el fabricante antes de realizar una compra si existen dudas sobre la compatibilidad con su vehículo.</p>
        </section>
        <section id="legislacion">
            <h2 class="fs-5 fw-bold titulo-oscuro mt-4">6. Legislación aplicable y fuero</h2>
            <p>Las presentes condiciones legales se rigen e interpretan de acuerdo con la legislación española. Para la resolución de cualquier controversia derivada del uso del sitio web, las partes se someten a los Juzgados y Tribunales de la ciudad de Madrid, salvo que la normativa vigente establezca otro fuero imperativo.</p>
            <p>Normativa de referencia:</p>
            <ul class="small">
                <li>Reglamento (UE) 2016/679 del Parlamento Europeo y del Consejo (RGPD)</li>
                <li>Ley Orgánica 3/2018, de 5 de diciembre, de Protección de Datos Personales (LOPDGDD)</li>
                <li>Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información (LSSI-CE)</li>
                <li>Real Decreto Legislativo 1/2007, de 16 de noviembre (TRLGDCU)</li>
            </ul>
        </section>
        <div class="mt-5 pt-4 border-top text-center">
            <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 me-2">
                <i class="ph ph-arrow-left me-1"></i> Volver al inicio
            </a>
            <a href="politica-privacidad.php" class="btn rounded-pill px-4" style="background:var(--azul-oscuro);color:#fff;">
                Política de Privacidad <i class="ph ph-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</main>
<?php require_once 'includes/footer.php'; ?>
