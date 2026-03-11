<?php
// 1. Definimos el título y cargamos nuestras secciones
$pagina_titulo = "Contacto | RecambiosPro";
require 'db.php';
include 'header.php';

// 2. Lógica sencilla para procesar el formulario
$mensaje_enviado = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Aquí en un proyecto real enviarías un email con la función mail() de PHP
    // o guardarías el mensaje en una tabla "mensajes" de la base de datos.
    // Por ahora, solo simulamos que se ha enviado con éxito.
    $mensaje_enviado = true;
}
?>

<main class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-primary">¿Necesitas ayuda?</h1>
        <p class="lead text-muted">Estamos aquí para asesorarte con las piezas de tu vehículo.</p>
    </div>

    <div class="row g-5">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 p-4 p-md-5">
                <h3 class="fw-bold mb-4">Envíanos un mensaje</h3>
                
                <?php if ($mensaje_enviado): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> ¡Gracias por contactarnos! Hemos recibido tu mensaje y te responderemos pronto.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="contacto.php">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nombre</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Tu nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="tu@email.com" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Asunto</label>
                        <select name="asunto" class="form-select" required>
                            <option value="">Selecciona un motivo...</option>
                            <option value="Duda sobre pieza">Duda sobre una pieza</option>
                            <option value="Problema con pedido">Problema con mi pedido</option>
                            <option value="Devolución">Devoluciones y garantías</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Mensaje</label>
                        <textarea name="mensaje" class="form-control" rows="5" placeholder="¿En qué podemos ayudarte?" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-pill">
                        <i class="bi bi-send me-2"></i> Enviar Mensaje
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 bg-primary text-white p-4 p-md-5 h-100">
                <h3 class="fw-bold mb-4">Información de Contacto</h3>
                
                <div class="d-flex align-items-start mb-4">
                    <i class="bi bi-geo-alt fs-2 me-3 text-warning"></i>
                    <div>
                        <h5 class="fw-bold mb-1">Nuestra Tienda</h5>
                        <p class="mb-0 text-white-50">Polígono Industrial Almoradi<br>Calle Municipal, Nave 12<br>03160 Almoradi, España</p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <i class="bi bi-telephone fs-2 me-3 text-warning"></i>
                    <div>
                        <h5 class="fw-bold mb-1">Teléfono</h5>
                        <p class="mb-0 text-white-50">+34 900 123 456<br>Lunes a Viernes: 9:00 - 18:00</p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <i class="bi bi-envelope fs-2 me-3 text-warning"></i>
                    <div>
                        <h5 class="fw-bold mb-1">Email</h5>
                        <p class="mb-0 text-white-50">soporte@recambiospro.com</p>
                    </div>
                </div>

                <hr class="border-light opacity-25 my-4">

                <h5 class="fw-bold mb-3">Síguenos</h5>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-outline-light rounded-circle"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-outline-light rounded-circle"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="btn btn-outline-light rounded-circle"><i class="bi bi-twitter"></i></a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>