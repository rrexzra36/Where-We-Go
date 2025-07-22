<?php
session_start();
// Fetch data first
require 'database/connection.php';
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM entri WHERE id = ?");
$stmt->execute([$id]);
$entri = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$entri) {
    echo "<p>Entry not found.</p>";
    exit();
}
$stmt_galeri = $conn->prepare("SELECT * FROM galeri WHERE id_entri = ?");
$stmt_galeri->execute([$id]);
$galeri = $stmt_galeri->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'templates/header.php'; ?>

<div class="entry-content-card">
    <div class="entry-actions">
        <div class="btn-group">
            <a href="edit.php?id=<?= $entri['id'] ?>" class="btn btn-sm btn-light"><i class="bi bi-pencil-fill me-1"></i> Edit</a>
            <button onclick="confirmDelete(<?= $entri['id'] ?>)" class="btn btn-sm btn-light"><i class="bi bi-trash-fill me-1"></i> Delete</button>
        </div>
    </div>

    <h1 class="entry-main-title">
        <?= htmlspecialchars($entri['judul']) ?>
    </h1>

    <div class="entry-meta">
        <span class="meta-item"><i class="bi bi-calendar3-fill me-1"></i> <?= date('d F Y', strtotime($entri['tanggal_kunjungan'])) ?></span>
        <span class="meta-item"><i class="bi bi-geo-alt-fill me-1"></i> <?= htmlspecialchars($entri['lokasi_nama']) ?></span>
        <span class="meta-item"><i class="bi bi-star-fill me-1"></i> <?= $entri['rating'] ?> out of 5</span>
        <span class="meta-item"><i class="bi bi-tag-fill me-1"></i> <?= htmlspecialchars($entri['kategori']) ?></span>
    </div>

    <hr class="my-4">

    <div class="row g-5">
        <div class="col-lg-12">
            <?php if (!empty($galeri)): ?>
                <div id="entryCarousel" class="carousel slide gallery-main-image mb-3" data-bs-ride="carousel">
                    <div class="carousel-inner" style="max-height:300px;">
                        <?php foreach ($galeri as $index => $foto): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <img src="assets/uploads/<?= htmlspecialchars($foto['nama_file']) ?>" alt="Photo <?= $index + 1 ?>" class="img-fluid rounded carousel-img" style="max-height:300px; object-fit:cover; width:100%; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#imageModal" data-img="assets/uploads/<?= htmlspecialchars($foto['nama_file']) ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($galeri) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#entryCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#entryCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Modal for image popup -->
                <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content bg-transparent border-0">
                            <div class="modal-body">
                                <img src="" id="modalImage" class="rounded" alt="Popup Image">
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var imageModal = document.getElementById('imageModal');
                        var modalImage = document.getElementById('modalImage');
                        document.querySelectorAll('.carousel-img').forEach(function(img) {
                            img.addEventListener('click', function() {
                                var src = this.getAttribute('data-img');
                                modalImage.src = src;
                            });
                        });
                        imageModal.addEventListener('hidden.bs.modal', function() {
                            modalImage.src = '';
                        });
                    });
                </script>
            <?php else: ?>
                <div class="gallery-main-image mb-3">
                    <img src="assets/uploads/<?= htmlspecialchars($entri['foto_utama']) ?>" alt="Main Photo" class="img-fluid rounded" style="max-height:300px; object-fit:cover; width:100%; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#imageModalSingle" data-img="assets/uploads/<?= htmlspecialchars($entri['foto_utama']) ?>">
                </div>
                <!-- Modal for single image popup -->
                <div class="modal fade" id="imageModalSingle" tabindex="-1" aria-labelledby="imageModalSingleLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content bg-transparent border-0">
                            <div class="modal-body p-0">
                                <img src="" id="modalImageSingle" class="img-fluid rounded w-100" alt="Popup Image">
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var imageModalSingle = document.getElementById('imageModalSingle');
                        var modalImageSingle = document.getElementById('modalImageSingle');
                        var mainImg = document.querySelector('.gallery-main-image img');
                        if (mainImg) {
                            mainImg.addEventListener('click', function() {
                                var src = this.getAttribute('data-img');
                                modalImageSingle.src = src;
                            });
                        }
                        imageModalSingle.addEventListener('hidden.bs.modal', function() {
                            modalImageSingle.src = '';
                        });
                    });
                </script>
            <?php endif; ?>

            <h5 class="section-title">Category</h5>
            <p class="entry-story"><?= nl2br(htmlspecialchars($entri['kategori'])) ?></p>

            <h5 class="section-title">Travel Notes</h5>
            <p class="entry-story"><?= nl2br(htmlspecialchars($entri['deskripsi'])) ?></p>

            <h5 class="section-title mt-5">Location Map</h5>
            <div id="map-detail" style="height: 350px; width: 100%; border-radius: 8px;"></div>
        </div>
    </div>
</div>

<script>
    // Script for Detail Map
    document.addEventListener('DOMContentLoaded', function() {
        var lat = <?= $entri['latitude'] ?>;
        var lng = <?= $entri['longitude'] ?>;
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