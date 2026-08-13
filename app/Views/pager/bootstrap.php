<!-- =================================================================================
// Paginación Bootstrap
// ================================================================================= -->
<?php
/**
 * Paginador Bootstrap 5 con íconos Font Awesome
 */
$pager->setSurroundCount(2);
?>
<nav aria-label="Navegación de páginas">
  <ul class="pagination justify-content-center my-4">

    <!-- Botón Anterior -->
    <?php if ($pager->hasPrevious()) : ?>
      <li class="page-item">
        <a class="page-link d-inline-flex align-items-center justify-content-center" href="<?= $pager->getPrevious() ?>" aria-label="Anterior">
        <i class="ti ti-arrow-left"></i>
        </a>
      </li>
    <?php else : ?>
      <li class="page-item">
        <span class="page-link d-inline-flex align-items-center justify-content-center"><i class="ti ti-arrow-left"></i></span>
      </li>
    <?php endif; ?>

    <!-- Números de página -->
    <?php foreach ($pager->links() as $link) : ?>
      <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
        <a class="page-link" href="<?= $link['uri'] ?>">
          <?= $link['title'] ?>
        </a>
      </li>
    <?php endforeach ?>

    <!-- Botón Siguiente -->
    <?php if ($pager->hasNext()) : ?>
      <li class="page-item">
        <a class="page-link d-inline-flex align-items-center justify-content-center" href="<?= $pager->getNext() ?>" aria-label="Siguiente">
        <i class="ti ti-arrow-right"></i>
        </a>
      </li>
    <?php else : ?>
      <li class="page-item">
        <span class="page-link d-inline-flex align-items-center justify-content-center"><i class="ti ti-arrow-right"></i></span>
      </li>
    <?php endif; ?>

  </ul>
</nav>

