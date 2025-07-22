<?php
session_start();
// Fetch data first
require 'database/connection.php';
if (!isset($_GET['id']) || empty($_GET['id'])) { header('Location: index.php'); exit(); }
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM entri WHERE id = ?");
$stmt->execute([$id]);
$entri = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$entri) { echo "<p>Entry not found.</p>"; exit(); }
$stmt_galeri = $conn->prepare("SELECT * FROM galeri WHERE id_entri = ?");
$stmt_galeri->execute([$id]);
$galeri = $stmt_galeri->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'templates/header.php'; ?>

<!-- Cover Image -->
<!-- <div class="entry-cover-image-wrapper">
    <img src="assets/uploads/<?= htmlspecialchars($entri['foto_utama']) ?>" class="entry-cover-image" alt="Cover Image">
</div> -->

<!-- Main Entry Content -->
<div class="entry-content-card">
    <!-- Action Buttons at Top Right -->
    <div class="entry-actions">
        <div class="btn-group">
            <a href="edit.php?id=<?= $entri['id'] ?>" class="btn btn-sm btn-light"><i class="bi bi-pencil-fill me-1"></i> Edit</a>
            <button onclick="confirmDelete(<?= $entri['id'] ?>)" class="btn btn-sm btn-light"><i class="bi bi-trash-fill me-1"></i> Delete</button>
        </div>
    </div>

    <!-- Title with Icon -->
    <h1 class="entry-main-title">
        <?= htmlspecialchars($entri['judul']) ?>
    </h1>

    <!-- Meta Properties -->
    <div class="entry-meta">
        <span class="meta-item"><i class="bi bi-calendar3-fill me-1"></i> <?= date('d F Y', strtotime($entri['tanggal_kunjungan'])) ?></span>
        <span class="meta-item"><i class="bi bi-geo-alt-fill me-1"></i> <?= htmlspecialchars($entri['lokasi_nama']) ?></span>
        <span class="meta-item"><i class="bi bi-star-fill me-1"></i> <?= $entri['rating'] ?> out of 5</span>
    </div>

    <hr class="my-4">

    <div class="row g-5">
        <div class="col-lg-12">
            <!-- Gallery -->
            <div class="gallery-main-image mb-3">
                <img src="assets/uploads/<?= htmlspecialchars($entri['foto_utama']) ?>" alt="Main Photo" class="img-fluid rounded" style="max-height:300px; object-fit:cover; width:100%;">
            </div>

            <!-- Category -->
            <h5 class="section-title">Category</h5>
            <p class="entry-story"><?= nl2br(htmlspecialchars($entri['kategori'])) ?></p>

            <!-- Travel Notes -->
            <h5 class="section-title">Travel Notes</h5>
            <p class="entry-story"><?= nl2br(htmlspecialchars($entri['deskripsi'])) ?></p>
            
            <h5 class="section-title mt-5">Location Map</h5>
            <div id="map-detail" style="height: 350px; width: 100%;"></div>
        </div>
    </div>
</div>

<script>
    // Script for Detail Map
    document.addEventListener('DOMContentLoaded', function() {
        var lat = <?= $entri['latitude'] ?>; var lng = <?= $entri['longitude'] ?>;
        var map = L.map('map-detail').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([lat, lng]).addTo(map).bindPopup('<b><?= htmlspecialchars($entri['lokasi_nama']) ?></b>').openPopup();
    });

    // Script for Delete Confirmation
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This travel story will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'proses_hapus.php?id=' + id;
            }
        })
    }
</script>

<?php include 'templates/footer.php'; ?>
