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

<!-- Page content starts here -->
<div class="content-header mb-5">
    <h2 class="greeting"><i class="bi <?= htmlspecialchars($icon_sapaan) ?> me-2"></i> <?= htmlspecialchars($sapaan) ?>!</h2>
    <p class="text-secondary">Welcome back to your travel journal.</p>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-6">
        <a href="tambah.php" class="action-card text-decoration-none">
            <div class="d-flex align-items-center">
                <div class="action-icon new-entry"><i class="bi bi-plus-lg"></i></div>
                <div class="ms-3">
                    <h5 class="mb-0">New Entry</h5>
                    <p class="mb-0 text-secondary small">Document your adventure.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="peta.php" class="action-card text-decoration-none">
            <div class="d-flex align-items-center">
                <div class="action-icon map-view"><i class="bi bi-geo-alt"></i></div>
                <div class="ms-3">
                    <h5 class="mb-0">View Map</h5>
                    <p class="mb-0 text-secondary small">Explore all your travel footprints.</p>
                </div>
            </div>
        </a>
    </div>
</div>

<h4 class="mb-3">Latest Journeys</h4>
<div class="list-container">
    <?php if (count($entri_list) > 0): ?>
        <?php foreach ($entri_list as $entri): ?>
            <div class="entry-item-container">
                <a href="entri.php?id=<?= $entri['id'] ?>" class="entry-item-link">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <img src="assets/uploads/<?= htmlspecialchars($entri['foto_utama']) ?>" class="entry-thumbnail" alt="<?= htmlspecialchars($entri['judul']) ?>">
                        </div>
                        <div class="col">
                            <h6 class="entry-title mb-1"><?= htmlspecialchars($entri['judul']) ?></h6>
                            <small class="text-secondary d-block"><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($entri['lokasi_nama']) ?> / <?= htmlspecialchars($entri['tanggal_kunjungan']) ?></small>
                        </div>
                    </div>
                </a>
                <button onclick="confirmDelete(<?= $entri['id'] ?>)" class="btn btn-sm btn-action-delete">
                    <i class="bi bi-trash-fill me-2"></i>
                </button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center p-5 bg-light rounded">
            <p class="mb-0">No travel stories yet. <a href="tambah.php">Add your first one!</a></p>
        </div>
    <?php endif; ?>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Anda yakin?',
            text: "Cerita perjalanan ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'proses_hapus.php?id=' + id;
            }
        })
    }
</script>

<?php include 'templates/footer.php'; ?>