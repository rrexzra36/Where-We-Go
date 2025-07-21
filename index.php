<?php
session_start();
require 'database/connection.php';
$stmt = $conn->query("SELECT * FROM entri ORDER BY tanggal_kunjungan DESC");
$entri_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

date_default_timezone_set('Asia/Jakarta');
$jam = date('H');
if ($jam >= 5 && $jam < 12) {
    $sapaan = "Good morning";
    $icon_sapaan = "bi-cloud-sun";
} elseif ($jam >= 12 && $jam < 18) {
    $sapaan = "Good afternoon";
    $icon_sapaan = "bi-brightness-high";
} else {
    $sapaan = "Good evening";
    $icon_sapaan = "bi-moon-stars";
}
?>

<?php include 'templates/header.php'; ?>

<!-- Konten halaman langsung dimulai di sini -->
<div class="content-header mb-5">
    <h2 class="greeting"><i class="bi <?= htmlspecialchars($icon_sapaan) ?> me-2"></i> <?= htmlspecialchars($sapaan) ?>!</h2>
    <p class="text-secondary">Selamat datang kembali di jurnal perjalanan Anda.</p>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-6">
        <a href="tambah.php" class="action-card text-decoration-none">
            <div class="d-flex align-items-center">
                <div class="action-icon new-entry"><i class="bi bi-plus-lg"></i></div>
                <div class="ms-3">
                    <h5 class="mb-0">Entri Baru</h5>
                    <p class="mb-0 text-secondary small">Dokumentasikan petualangan Anda.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="peta.php" class="action-card text-decoration-none">
            <div class="d-flex align-items-center">
                <div class="action-icon map-view"><i class="bi bi-geo-alt"></i></div>
                <div class="ms-3">
                    <h5 class="mb-0">Lihat Peta</h5>
                    <p class="mb-0 text-secondary small">Jelajahi semua jejak perjalanan.</p>
                </div>
            </div>
        </a>
    </div>
</div>

<h4 class="mb-3">Perjalanan Terbaru</h4>
<div class="list-container">
    <?php if (count($entri_list) > 0): ?>
        <?php foreach ($entri_list as $entri): ?>
            <a href="entri.php?id=<?= $entri['id'] ?>" class="entry-item text-decoration-none">
                <div class="row g-3 align-items-center">
                    <div class="col-auto"><img src="assets/uploads/<?= htmlspecialchars($entri['foto_utama']) ?>" class="entry-thumbnail" alt="<?= htmlspecialchars($entri['judul']) ?>"></div>
                    <div class="col">
                        <h6 class="entry-title mb-1"><?= htmlspecialchars($entri['judul']) ?></h6><small class="text-secondary d-block"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($entri['lokasi_nama']) ?></small>
                    </div>
                    <div class="col-md-3 text-md-end"><small class="text-secondary"><?= date('d M Y', strtotime($entri['tanggal_kunjungan'])) ?></small></div>
                    <div class="col-auto"><i class="bi bi-chevron-right text-secondary"></i></div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center p-5 bg-light rounded">
            <p class="mb-0">Belum ada cerita perjalanan. <a href="tambah.php">Ayo tambahkan yang pertama!</a></p>
        </div>
    <?php endif; ?>
</div>

<?php include 'templates/footer.php'; ?>