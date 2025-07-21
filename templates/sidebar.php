<?php
// Mendeteksi nama file halaman saat ini, contoh: 'index.php'
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="offcanvas offcanvas-start sidebar" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarOffcanvasLabel">Jurnal Perjalanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= ($currentPage == 'index.php') ? 'active' : '' ?>" href="index.php">
                    <i class="bi bi-house-door-fill me-2"></i> Home
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currentPage == 'tambah.php') ? 'active' : '' ?>" href="tambah.php">
                    <i class="bi bi-plus-square me-2"></i> Tambah Entri
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currentPage == 'peta.php') ? 'active' : '' ?>" href="peta.php">
                    <i class="bi bi-map me-2"></i> Peta Global
                </a>
            </li>
        </ul>
    </div>
</div>