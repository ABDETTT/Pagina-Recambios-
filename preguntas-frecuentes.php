<?php
$pagina_titulo = 'Preguntas Frecuentes (FAQ) | AutoStock';
require_once 'includes/header.php';
?>
<main class="container py-5" style="max-width: 860px;">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none" style="color:var(--azul-oscuro);">Inicio</a></li>
            <li class="breadcrumb-item active">Preguntas Frecuentes</li>
        </ol>
    </nav>
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        <div class="mb-4 pb-3 border-bottom text-center">
            <h1 class="fw-bold titulo-oscuro fs-2">Preguntas Frecuentes (FAQ)</h1>
            <p class="text-muted mb-0">Resuelve tus dudas sobre envíos, devoluciones y productos.</p>
        </div>
        <div class="accordion accordion-flush" id="accordionFAQ">
            <h3 class="fs-5 fw-bold titulo-oscuro mt-4 mb-3"><i class="ph ph-package text-naranja me-2"></i>Pedidos y Envíos</h3>
            <div class="accordion-item bg-transparent border-bottom">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed bg-transparent fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        ¿Cuánto tarda en llegar mi pedido?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionFAQ">
                    <div class="accordion-body text-muted small">
                        Para envíos a la Península, el plazo de entrega habitual es de 24 a 48 horas laborables desde que el pedido sale de nuestras instalaciones. Recibirás un correo electrónico con el número de seguimiento en cuanto tu pedido sea enviado.
                    </div>
                </div>
            </div>
            <div class="accordion-item bg-transparent border-bottom">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed bg-transparent fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        ¿Cuáles son los gastos de envío?
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionFAQ">
                    <div class="accordion-body text-muted small">
                        Los gastos de envío estándar son 4,99€. Ofrecemos envío gratuito para todos los pedidos superiores a 50€.
                    </div>
                </div>
            </div>
            <h3 class="fs-5 fw-bold titulo-oscuro mt-5 mb-3"><i class="ph ph-arrow-bend-up-left text-naranja me-2"></i>Devoluciones y Garantía</h3>
            <div class="accordion-item bg-transparent border-bottom">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed bg-transparent fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        ¿Cómo puedo devolver un producto?
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionFAQ">
                    <div class="accordion-body text-muted small">
                        Dispones de 14 días naturales para realizar cualquier devolución. Puedes gestionarla desde tu <a href="panel_cliente.php" class="text-naranja text-decoration-none">Panel de Cliente</a> en el apartado de pedidos, o contactando directamente con nuestro servicio de atención al cliente a través del <a href="contacto.php" class="text-naranja text-decoration-none">formulario de contacto</a>.
                    </div>
                </div>
            </div>
            <div class="accordion-item bg-transparent border-bottom">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed bg-transparent fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        ¿Qué garantía tienen las piezas?
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionFAQ">
                    <div class="accordion-body text-muted small">
                        Todas nuestras piezas de recambio son nuevas y cuentan con una garantía legal de 3 años contra defectos de fabricación, tal como estipula la ley vigente en España.
                    </div>
                </div>
            </div>
            <h3 class="fs-5 fw-bold titulo-oscuro mt-5 mb-3"><i class="ph ph-wrench text-naranja me-2"></i>Productos y Compatibilidad</h3>
            <div class="accordion-item bg-transparent border-bottom">
                <h2 class="accordion-header" id="headingFive">
                    <button class="accordion-button collapsed bg-transparent fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        ¿Cómo sé si una pieza es compatible con mi vehículo?
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionFAQ">
                    <div class="accordion-body text-muted small">
                        En la ficha de cada producto encontrarás un apartado de "Vehículos compatibles". Si sigues teniendo dudas, te recomendamos que contactes con nuestro soporte técnico facilitándonos el número de bastidor (VIN) de tu coche, y nosotros te confirmaremos la compatibilidad.
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-5 text-center">
            <p class="text-muted small">¿No has encontrado la respuesta que buscabas?</p>
            <a href="contacto.php" class="btn btn-naranja rounded-pill px-4 fw-bold">Contactar con Soporte</a>
        </div>
    </div>
</main>
<?php require_once 'includes/footer.php'; ?>
