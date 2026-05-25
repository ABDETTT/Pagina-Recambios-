<?php
$es_admin = (
    (isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true) ||
    (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin')
);
$foto = !empty($p['imagen_url']) ? $p['imagen_url'] : (!empty($p['imagen']) ? 'img/productos/' . $p['imagen'] : 'img/productos/placeholder.png');
?>
<article class="col-md-4 anim fade-up">
  <div class="card h-100 card-product shadow-sm">
    <div class="cursor-pointer position-relative card-img-wrap" data-bs-toggle="modal" data-bs-target="#modalProducto<?= $p['id'] ?>">
      <div class="bg-light text-center border-bottom d-flex align-items-center justify-content-center" style="height: 180px; overflow:hidden;">
          <img src="<?= htmlspecialchars($foto) ?>" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="<?= htmlspecialchars($p['nombre']) ?>" onerror="this.src='https://placehold.co/400x300?text=Sin+Imagen'">
      </div>
    </div>
    <div class="card-body d-flex flex-column">
      <span class="badge mb-2 align-self-start" style="font-size: 0.7rem; background-color: var(--azul-oscuro);"><?= htmlspecialchars($p['cat_nombre'] ?? 'General') ?></span>
      <h6 class="fw-bold text-dark cursor-pointer text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalProducto<?= $p['id'] ?>">
          <?= htmlspecialchars($p['nombre']) ?>
      </h6>
      <p class="text-naranja fw-bold fs-5 mb-3"><?= number_format($p['precio'], 2) ?> €</p>
      <form action="scripts/agregar_carrito.php" method="POST" class="mt-auto">
        <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
        <button type="submit" class="btn btn-naranja w-100 btn-sm fw-bold rounded-pill" <?= (($p['stock'] ?? 0) <= 0) ? 'disabled' : '' ?>>
            <i class="bi bi-cart-plus me-1"></i> Añadir
        </button>
      </form>
      <?php if ($es_admin): ?>
          <div class="d-flex gap-2 mt-2 w-100">
              <a href="admin/editar_producto.php?id=<?= $p['id'] ?>" class="btn btn-warning w-50 btn-sm fw-bold rounded-pill">Editar</a>
              <a href="admin/eliminar_producto.php?id=<?= $p['id'] ?>" class="btn btn-danger w-50 btn-sm fw-bold rounded-pill" onclick="return confirm('¿Seguro que deseas eliminar este producto?');">Borrar</a>
          </div>
      <?php endif; ?>
    </div>
  </div>
</article>
<div class="modal fade" id="modalProducto<?= $p['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $p['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 p-md-5 pt-0">
         <div class="row g-4 align-items-center">
            <div class="col-md-6 text-center">
                <img src="<?= htmlspecialchars($foto) ?>" class="img-fluid rounded-3 shadow-sm w-100" style="object-fit: cover; max-height: 350px;" alt="<?= htmlspecialchars($p['nombre']) ?>" onerror="this.src='https://placehold.co/400x300?text=Sin+Imagen'">
            </div>
            <div class="col-md-6 d-flex flex-column h-100">
                <div>
                    <span class="badge bg-dark mb-2"><?= htmlspecialchars($p['cat_nombre'] ?? 'General') ?></span>
                    <h3 class="fw-bold mb-3" id="modalLabel<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></h3>
                    <h4 class="text-naranja fw-bold display-6 mb-3"><?= number_format($p['precio'], 2) ?> €</h4>
                    <p class="text-muted mb-4" style="font-size: 0.95rem;">
                        <?= !empty($p['descripcion']) ? nl2br(htmlspecialchars($p['descripcion'])) : 'Sin descripción detallada para esta pieza.' ?>
                    </p>
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                        <span class="fw-bold me-2">Disponibilidad:</span>
                        <?php if(($p['stock'] ?? 0) > 0): ?>
                            <span class="badge bg-success-subtle text-success px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>En stock (<?= $p['stock'] ?> uds)</span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="bi bi-x-circle-fill me-1"></i>Agotado</span>
                        <?php endif; ?>
                    </div>
                    <form action="scripts/agregar_carrito.php" method="POST" class="d-flex gap-2 mb-4">
                        <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-naranja btn-lg flex-grow-1 fw-bold rounded-pill shadow-sm" <?= (($p['stock'] ?? 0) <= 0) ? 'disabled' : '' ?>>
                            <i class="bi bi-cart-plus me-2"></i> Añadir al carrito
                        </button>
                    </form>
                </div>
            </div>
         </div>
      </div>
    </div>
  </div>
</div>