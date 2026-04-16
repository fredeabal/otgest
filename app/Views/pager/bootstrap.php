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
        <iconify-icon icon="solar:alt-arrow-left-outline"></iconify-icon>
        </a>
      </li>
    <?php else : ?>
      <li class="page-item">
        <span class="page-link d-inline-flex align-items-center justify-content-center"><iconify-icon icon="solar:alt-arrow-left-outline"></iconify-icon></span>
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
        <iconify-icon icon="solar:alt-arrow-right-outline"></iconify-icon>
        </a>
      </li>
    <?php else : ?>
      <li class="page-item">
        <span class="page-link d-inline-flex align-items-center justify-content-center"><iconify-icon icon="solar:alt-arrow-right-outline"></iconify-icon></span>
      </li>
    <?php endif; ?>

  </ul>
</nav>

