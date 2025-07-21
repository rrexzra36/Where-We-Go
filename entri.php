<?php
session_start();
// Ambil data dulu
require 'database/connection.php';
if (!isset($_GET['id']) || empty($_GET['id'])) { header('Location: index.php'); exit(); }
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM entri WHERE id = ?");
$stmt->execute([$id]);
$entri = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$entri) { echo "<p>Entri tidak ditemukan.</p>"; exit(); }
$stmt_galeri = $conn->prepare("SELECT * FROM galeri WHERE id_entri = ?");
$stmt_galeri->execute([$id]);
$galeri = $stmt_galeri->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'templates/header.php'; ?>

<!-- Cover Image -->
<div class="entry-cover-image-wrapper">
    <img src="assets/uploads/<?= htmlspecialchars($entri['foto_utama']) ?>" class="entry-cover-image" alt="Cover Image">
</div>

<!-- Main Entry Content -->
<div class="entry-content-card">
    <!-- Tombol Aksi di Pojok Kanan Atas -->
    <div class="entry-actions">
        <div class="btn-group">
            <a href="edit.php?id=<?= $entri['id'] ?>" class="btn btn-sm btn-light"><i class="bi bi-pencil me-1"></i> Edit</a>
            <button onclick="confirmDelete(<?= $entri['id'] ?>)" class="btn btn-sm btn-light"><i class="bi bi-trash me-1"></i> Hapus</button>
        </div>
    </div>

    <!-- Judul dengan Ikon -->
    <h1 class="entry-main-title">
        <span class="entry-title-icon">📄</span>
        <?= htmlspecialchars($entri['judul']) ?>
    </h1>

    <!-- Properti Meta -->
    <div class="entry-meta">
        <span class="meta-item"><i class="bi bi-calendar3 me-1"></i> <?= date('d F Y', strtotime($entri['tanggal_kunjungan'])) ?></span>
        <span class="meta-item"><i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($entri['lokasi_nama']) ?></span>
        <span class="meta-item"><i class="bi bi-star me-1"></i> <?= $entri['rating'] ?> dari 5</span>
    </div>

    <hr class="my-4">

    <!-- Layout Dua Kolom -->
    <div class="row g-5">
        <!-- Kolom Kiri: Cerita & Peta -->
        <div class="col-lg-8">
            <h5 class="section-title">Catatan Perjalanan</h5>
            <p class="entry-story"><?= nl2br(htmlspecialchars($entri['deskripsi'])) ?></p>
            
            <h5 class="section-title mt-5">Peta Lokasi</h5>
            <div id="map-detail" style="height: 350px; width: 100%;"></div>
        </div>

        <!-- Kolom Kanan: Galeri -->
        <div class="col-lg-4">
            <h5 class="section-title">Galeri Foto</h5>
            <div class="gallery-sidebar">
                <?php foreach($galeri as $foto): ?>
                    <img src="assets/uploads/<?= htmlspecialchars($foto['nama_file']) ?>" alt="Galeri foto">
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Script untuk Peta Detail
    document.addEventListener('DOMContentLoaded', function() {
        var lat = <?= $entri['latitude'] ?>; var lng = <?= $entri['longitude'] ?>;
        var map = L.map('map-detail').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([lat, lng]).addTo(map).bindPopup('<b><?= htmlspecialchars($entri['judul']) ?></b>').openPopup();
    });

    // Script untuk Konfirmasi Hapus
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
