<?php 
$foto = !empty($p['imagen']) ? '../assets/img/productos/' . $p['imagen'] : '../assets/img/productos/placeholder.png'; 
?>
<div class="col-md-4">
  <div class="card h-100 card-product shadow-sm">
    <div class="cursor-pointer position-relative" data-bs-toggle="modal" data-bs-target="#modalProducto<?php echo $p['id']; ?>">
      <div class="bg-light text-center border-bottom d-flex align-items-center justify-content-center" style="height: 180px; overflow:hidden;">
          <img src="<?php echo $foto; ?>" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($p['nombre']); ?>" onerror="this.src='https://placehold.co/400x300?text=Sin+Imagen'">
      </div>
    </div>

    <div class="card-body d-flex flex-column">
      <span class="badge bg-secondary mb-2 align-self-start" style="font-size: 0.7rem;"><?php echo htmlspecialchars($p['cat_nombre'] ?? 'General'); ?></span>
      <h6 class="fw-bold text-dark cursor-pointer text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalProducto<?php echo $p['id']; ?>">
          <?php echo htmlspecialchars($p['nombre']); ?>
      </h6>
      <p class="text-primary fw-bold fs-5 mb-3"><?php echo number_format($p['precio'], 2); ?> €</p>
      <form action="/RecambiosPro/actions/agregar_carrito.php" method="POST" class="mt-auto">
        <input type="hidden" name="id_producto" value="<?php echo $p['id']; ?>">
        <button type="submit" class="btn btn-primary w-100 btn-sm fw-bold rounded-pill" <?php echo (($p['stock'] ?? 0) <= 0) ? 'disabled' : ''; ?>>
            <i class="bi bi-cart-plus me-1"></i> Añadir
        </button>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalProducto<?php echo $p['id']; ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo $p['id']; ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 p-md-5 pt-0">
         <div class="row g-4 align-items-center">
            <div class="col-md-6 text-center">
                <img src="<?php echo $foto; ?>" class="img-fluid rounded-3 shadow-sm w-100" style="object-fit: cover; max-height: 350px;" alt="<?php echo htmlspecialchars($p['nombre']); ?>" onerror="this.src='https://placehold.co/400x300?text=Sin+Imagen'">
            </div>
            
            <div class="col-md-6 d-flex flex-column h-100">
                <div>
                    <span class="badge bg-dark mb-2"><?php echo htmlspecialchars($p['cat_nombre'] ?? 'General'); ?></span>
                    <h3 class="fw-bold mb-3" id="modalLabel<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></h3>
                    <h4 class="text-primary fw-bold display-6 mb-3"><?php echo number_format($p['precio'], 2); ?> €</h4>
                    <p class="text-muted mb-4" style="font-size: 0.95rem;">
                        <?php echo !empty($p['descripcion']) ? nl2br(htmlspecialchars($p['descripcion'])) : 'Sin descripción detallada para esta pieza.'; ?>
                    </p>
                    
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                        <span class="fw-bold me-2">Disponibilidad:</span>
                        <?php if(($p['stock'] ?? 0) > 0): ?>
                            <span class="badge bg-success-subtle text-success px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>En stock (<?php echo $p['stock']; ?> uds)</span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="bi bi-x-circle-fill me-1"></i>Agotado</span>
                        <?php endif; ?>
                    </div>
                    
                    <form action="/RecambiosPro/actions/agregar_carrito.php" method="POST" class="d-flex gap-2 mb-4">
                        <input type="hidden" name="id_producto" value="<?php echo $p['id']; ?>">
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1 fw-bold rounded-pill shadow-sm" <?php echo (($p['stock'] ?? 0) <= 0) ? 'disabled' : ''; ?>>
                            <i class="bi bi-cart-plus me-2"></i> Añadir al carrito
                        </button>
                    </form>
                </div>

                <?php
                $relacionados = [];
                if (!empty($p['categoria_id'])) {
                    try {
                        $stmt_rel = $pdo->prepare("SELECT id, nombre, precio, imagen FROM productos WHERE categoria_id = :cid AND id != :pid ORDER BY id DESC LIMIT 3");
                        $stmt_rel->execute(['cid' => $p['categoria_id'], 'pid' => $p['id']]);
                        $relacionados = $stmt_rel->fetchAll();
                    } catch (PDOException $e) {
                        $relacionados = [];
                    }
                }
                ?>
                
                <?php if(count($relacionados) > 0): ?>
                <div class="mt-auto pt-3 border-top">
                    <h6 class="fw-bold mb-3 text-uppercase small text-muted"><i class="bi bi-stars text-warning me-1"></i> También te puede interesar</h6>
                    <div class="row g-2">
                        <?php foreach($relacionados as $rel): ?>
                            <?php $foto_rel = !empty($rel['imagen']) ? '../assets/img/productos/' . $rel['imagen'] : '../assets/img/productos/placeholder.png'; ?>
                            <div class="col-4 text-center">
                                <a href="catalogo.php?s=<?php echo urlencode($rel['nombre']); ?>" class="text-decoration-none text-dark d-block hover-scale">
                                    <img src="<?php echo $foto_rel; ?>" class="img-fluid rounded border mb-2 w-100" style="height: 60px; object-fit: cover;" alt="<?php echo htmlspecialchars($rel['nombre']); ?>" onerror="this.src='https://placehold.co/100x100?text=Img'">
                                    <p class="small mb-0 text-truncate fw-medium" style="font-size: 0.75rem;"><?php echo htmlspecialchars($rel['nombre']); ?></p>
                                    <span class="fw-bold text-primary" style="font-size: 0.8rem;"><?php echo number_format($rel['precio'], 2); ?> €</span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
         </div>
      </div>
    </div>
  </div>
</div>