<?php
$pagina_titulo = "Video Tutoriales | RecambiosPro";
require 'db.php';
include 'header.php';
?>

<main class="bg-light pb-5">
    <section class="bg-dark text-white py-5 mb-5 text-center" style="background: linear-gradient(rgba(0,36,107,0.9), rgba(0,0,0,0.9)), url('img/img4.png'); background-size: cover; background-position: center;">
        <div class="container py-4">
            <h1 class="display-4 fw-bold mb-3"><i class="bi bi-play-circle text-warning me-3"></i>Aprende y Ahorra</h1>
            <p class="lead opacity-75 mx-auto" style="max-width: 700px;">
                Descubre cómo instalar tus propios recambios paso a paso. Ahorra dinero en el taller y mantén tu coche en perfectas condiciones con las guías de nuestros expertos.
            </p>
        </div>
    </section>

    <div class="container mb-4">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <button class="btn btn-primary rounded-pill px-4 fw-bold">Todos</button>
            <button class="btn btn-outline-secondary rounded-pill px-4 bg-white">Mantenimiento Básico</button>
            <button class="btn btn-outline-secondary rounded-pill px-4 bg-white">Frenos</button>
            <button class="btn btn-outline-secondary rounded-pill px-4 bg-white">Motor y Fluidos</button>
        </div>
    </div>

    <div class="container">
        <div class="row g-4">
            
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 card-hover rounded-4 overflow-hidden">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/5m3O5Sna4gM" title="Cambio de aceite" allowfullscreen></iframe>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary-subtle text-primary">Fluidos</span>
                            <span class="badge bg-success"><i class="bi bi-wrench me-1"></i>Fácil</span>
                        </div>
                        <h5 class="fw-bold mb-2">Cómo cambiar el aceite y el filtro</h5>
                        <p class="text-muted small mb-4">Guía paso a paso para vaciar el cárter, cambiar el filtro de aceite y rellenar con aceite nuevo sin ensuciar tu garaje.</p>
                        <a href="catalogo.php?categoria=1" class="btn btn-outline-primary btn-sm mt-auto fw-bold rounded-pill">Ver Aceites Compatibles</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 card-hover rounded-4 overflow-hidden">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/JZbX2sK1c6Y" title="Cambiar pastillas de freno" allowfullscreen></iframe>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-secondary-subtle text-secondary">Frenos</span>
                            <span class="badge bg-warning text-dark"><i class="bi bi-wrench me-1"></i>Medio</span>
                        </div>
                        <h5 class="fw-bold mb-2">Sustitución de pastillas de freno delanteras</h5>
                        <p class="text-muted small mb-4">Aprende a desmontar la pinza de freno, retraer el pistón e instalar tus nuevas pastillas de freno de forma segura.</p>
                        <a href="catalogo.php?categoria=2" class="btn btn-outline-primary btn-sm mt-auto fw-bold rounded-pill">Comprar Pastillas</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 card-hover rounded-4 overflow-hidden">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/Rk540JgO1_I" title="Cambiar batería" allowfullscreen></iframe>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-info-subtle text-info">Eléctrico</span>
                            <span class="badge bg-success"><i class="bi bi-wrench me-1"></i>Fácil</span>
                        </div>
                        <h5 class="fw-bold mb-2">Cambiar la batería del coche</h5>
                        <p class="text-muted small mb-4">El coche no arranca? Te enseñamos el orden correcto para desconectar los bornes y cambiar la batería de forma segura.</p>
                        <a href="catalogo.php?categoria=3" class="btn btn-outline-primary btn-sm mt-auto fw-bold rounded-pill">Ver Baterías</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 card-hover rounded-4 overflow-hidden">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/vO82-fR1p6g" title="Cambiar correa distribución" allowfullscreen></iframe>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-dark-subtle text-dark">Motor</span>
                            <span class="badge bg-danger"><i class="bi bi-wrench me-1"></i>Difícil</span>
                        </div>
                        <h5 class="fw-bold mb-2">Kit de Distribución Completo</h5>
                        <p class="text-muted small mb-4">Operación avanzada: cambio de correa de distribución, tensores y bomba de agua. Requiere conocimientos mecánicos.</p>
                        <a href="catalogo.php" class="btn btn-outline-primary btn-sm mt-auto fw-bold rounded-pill">Buscar Kits</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 card-hover rounded-4 overflow-hidden">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/S22PqHl1TTo" title="Cambiar filtro habitáculo" allowfullscreen></iframe>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary-subtle text-primary">Mantenimiento</span>
                            <span class="badge bg-success"><i class="bi bi-wrench me-1"></i>Fácil</span>
                        </div>
                        <h5 class="fw-bold mb-2">Cambio de filtro de habitáculo (Polen)</h5>
                        <p class="text-muted small mb-4">Mejora la calidad del aire del aire acondicionado de tu coche cambiando este filtro en menos de 10 minutos.</p>
                        <a href="catalogo.php" class="btn btn-outline-primary btn-sm mt-auto fw-bold rounded-pill">Ver Filtros</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow border-0 bg-primary text-white rounded-4 overflow-hidden d-flex align-items-center justify-content-center text-center p-5 card-hover">
                    <i class="bi bi-youtube display-1 text-white mb-3 opacity-75"></i>
                    <h4 class="fw-bold mb-3">¿No encuentras tu tutorial?</h4>
                    <p class="small opacity-75 mb-4">Suscríbete a nuestro canal oficial para no perderte las nuevas guías de mecánica que subimos cada semana.</p>
                    <a href="#" class="btn btn-light text-primary fw-bold rounded-pill px-4">Ir a YouTube</a>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include 'footer.php'; ?>