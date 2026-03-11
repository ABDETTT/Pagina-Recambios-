<?php
$pagina_titulo = "Video Tutoriales | RecambiosPro";
require '../includes/db.php';
include '../includes/header.php';

$tutoriales = [
    [
        "id_yt" => "5m3O5Sna4gM",
        "filtro" => "motor_fluidos",
        "badge_color" => "primary",
        "badge_texto" => "Fluidos",
        "dif_color" => "success",
        "dif_texto" => "Fácil",
        "titulo" => "Cómo cambiar el aceite y el filtro",
        "desc" => "Guía paso a paso para vaciar el cárter, cambiar el filtro de aceite y rellenar con aceite nuevo sin ensuciar tu garaje.",
        "enlace" => "catalogo.php?categoria=1",
        "btn_texto" => "Ver Aceites Compatibles"
    ],
    [
        "id_yt" => "JZbX2sK1c6Y",
        "filtro" => "frenos",
        "badge_color" => "secondary",
        "badge_texto" => "Frenos",
        "dif_color" => "warning text-dark",
        "dif_texto" => "Medio",
        "titulo" => "Sustitución de pastillas de freno delanteras",
        "desc" => "Aprende a desmontar la pinza de freno, retraer el pistón e instalar tus nuevas pastillas de freno de forma segura.",
        "enlace" => "catalogo.php?categoria=2",
        "btn_texto" => "Comprar Pastillas"
    ],
    [
        "id_yt" => "Rk540JgO1_I",
        "filtro" => "mantenimiento",
        "badge_color" => "info",
        "badge_texto" => "Eléctrico",
        "dif_color" => "success",
        "dif_texto" => "Fácil",
        "titulo" => "Cambiar la batería del coche",
        "desc" => "El coche no arranca? Te enseñamos el orden correcto para desconectar los bornes y cambiar la batería de forma segura.",
        "enlace" => "catalogo.php?categoria=3",
        "btn_texto" => "Ver Baterías"
    ],
    [
        "id_yt" => "vO82-fR1p6g",
        "filtro" => "motor_fluidos",
        "badge_color" => "dark",
        "badge_texto" => "Motor",
        "dif_color" => "danger",
        "dif_texto" => "Difícil",
        "titulo" => "Kit de Distribución Completo",
        "desc" => "Operación avanzada: cambio de correa de distribución, tensores y bomba de agua. Requiere conocimientos mecánicos.",
        "enlace" => "catalogo.php",
        "btn_texto" => "Buscar Kits"
    ],
    [
        "id_yt" => "S22PqHl1TTo",
        "filtro" => "mantenimiento",
        "badge_color" => "primary",
        "badge_texto" => "Mantenimiento",
        "dif_color" => "success",
        "dif_texto" => "Fácil",
        "titulo" => "Cambio de filtro de habitáculo (Polen)",
        "desc" => "Mejora la calidad del aire del aire acondicionado de tu coche cambiando este filtro en menos de 10 minutos.",
        "enlace" => "catalogo.php",
        "btn_texto" => "Ver Filtros"
    ]
];
?>

<style>
    .desc-clamp {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }

    .tarjeta-tut {
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* CORRECCIÓN DE LEGIBILIDAD EN HOVER */
    #botones-filtro .btn-outline-secondary:hover {
        background-color: #495057 !important; /* Gris oscuro */
        color: #ffffff !important; /* Texto blanco puro */
        border-color: #495057 !important;
    }

    /* Estilo para el botón activo */
    #botones-filtro .btn-primary.active {
        background-color: #0d6efd !important;
        color: #ffffff !important;
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
    }
</style>

<main class="bg-light pb-5">
    <section class="bg-dark text-white py-5 mb-5 text-center" style="background: linear-gradient(rgba(0,36,107,0.9), rgba(0,0,0,0.9)), url('../assets/img/img4.png'); background-size: cover; background-position: center;">
        <div class="container py-4">
            <h1 class="display-4 fw-bold mb-3"><i class="bi bi-play-circle text-warning me-3"></i>Aprende y Ahorra</h1>
            <p class="lead opacity-75 mx-auto" style="max-width: 700px;">
                Descubre cómo instalar tus propios recambios paso a paso. Ahorra dinero en el taller y mantén tu coche en perfectas condiciones con las guías de nuestros expertos.
            </p>
        </div>
    </section>

    <div class="container mb-4">
        <div class="d-flex flex-wrap gap-2 justify-content-center" id="botones-filtro">
            <button class="btn btn-primary rounded-pill px-4 fw-bold active" onclick="filtrarVideos('todos', this)"><i class="bi bi-grid-fill me-2"></i>Todos</button>
            <button class="btn btn-outline-secondary rounded-pill px-4 bg-white text-dark" onclick="filtrarVideos('mantenimiento', this)"><i class="bi bi-tools me-2"></i>Mantenimiento Básico</button>
            <button class="btn btn-outline-secondary rounded-pill px-4 bg-white text-dark" onclick="filtrarVideos('frenos', this)"><i class="bi bi-record-circle me-2"></i>Frenos</button>
            <button class="btn btn-outline-secondary rounded-pill px-4 bg-white text-dark" onclick="filtrarVideos('motor_fluidos', this)"><i class="bi bi-gear-fill me-2"></i>Motor y Fluidos</button>
        </div>
    </div>

    <div class="container">
        <div class="row g-4" id="contenedor-videos">
            
            <?php foreach($tutoriales as $tut): ?>
                <div class="col-md-6 col-lg-4 tarjeta-tut" data-categoria="<?php echo $tut['filtro']; ?>">
                    <div class="card h-100 shadow-sm border-0 card-hover rounded-4 overflow-hidden">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/<?php echo $tut['id_yt']; ?>" loading="lazy" title="<?php echo htmlspecialchars($tut['titulo']); ?>" allowfullscreen></iframe>
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-<?php echo $tut['badge_color']; ?>-subtle text-<?php echo $tut['badge_color']; ?>"><?php echo htmlspecialchars($tut['badge_texto']); ?></span>
                                <span class="badge bg-<?php echo $tut['dif_color']; ?>"><i class="bi bi-wrench me-1"></i><?php echo htmlspecialchars($tut['dif_texto']); ?></span>
                            </div>
                            <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($tut['titulo']); ?></h5>
                            <p class="text-muted small mb-4 desc-clamp"><?php echo htmlspecialchars($tut['desc']); ?></p>
                            <a href="<?php echo $tut['enlace']; ?>" class="btn btn-outline-primary btn-sm mt-auto fw-bold rounded-pill"><?php echo htmlspecialchars($tut['btn_texto']); ?></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="col-md-6 col-lg-4 tarjeta-tut" data-categoria="todos">
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

<script>
function filtrarVideos(categoria, boton) {
    const tarjetas = document.querySelectorAll('.tarjeta-tut');
    const botones = document.querySelectorAll('#botones-filtro button');

    botones.forEach(btn => {
        btn.classList.remove('btn-primary', 'active');
        btn.classList.add('btn-outline-secondary', 'bg-white', 'text-dark');
    });

    boton.classList.remove('btn-outline-secondary', 'bg-white', 'text-dark');
    boton.classList.add('btn-primary', 'active');

    tarjetas.forEach(tarjeta => {
        if (categoria === 'todos' || tarjeta.getAttribute('data-categoria') === categoria) {
            tarjeta.style.display = 'block';
        } else {
            tarjeta.style.display = 'none';
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>