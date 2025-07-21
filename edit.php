<?php
session_start();
require 'database/connection.php';

// Cek apakah ada ID, jika tidak, kembalikan ke index
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM entri WHERE id = ?");
$stmt->execute([$id]);
$entri = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika entri dengan ID tersebut tidak ada
if (!$entri) {
    echo "Entri tidak ditemukan.";
    exit();
}

// Ubah string kategori menjadi array untuk dicocokkan di checkbox
$kategori_tersimpan = explode(',', $entri['kategori']);
?>

<?php include 'templates/header.php'; ?>

<!-- Tambahkan CSS untuk Plugin Geocoder -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<div class="page-content-wrapper">
    <main class="container-fluid p-4 p-md-5">
        <div class="content-header mb-4">
            <h2 class="greeting"><i class="bi bi-pencil-square me-2"></i> Edit Kenangan</h2>
            <p class="text-secondary">Perbarui detail petualangan Anda.</p>
        </div>

        <div class="form-container-card">
            <form action="proses_edit.php" method="POST" enctype="multipart/form-data">
                <!-- Kirim ID entri secara tersembunyi -->
                <input type="hidden" name="id" value="<?= $entri['id'] ?>">

                <div class="row g-4">
                    <div class="col-12">
                        <label for="judul" class="form-label">Judul Perjalanan</label>
                        <input type="text" class="form-control form-control-lg" id="judul" name="judul" value="<?= htmlspecialchars($entri['judul']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_kunjungan" class="form-label">Tanggal Kunjungan</label>
                        <input type="date" class="form-control" id="tanggal_kunjungan" name="tanggal_kunjungan" value="<?= $entri['tanggal_kunjungan'] ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="rating" class="form-label">Rating Pengalaman (1-5)</label>
                        <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" value="<?= $entri['rating'] ?>" required>
                    </div>
                    <div class="col-12">
                        <label for="lokasi_nama" class="form-label">Nama Lokasi</label>
                        <input type="text" class="form-control" id="lokasi_nama" name="lokasi_nama" value="<?= htmlspecialchars($entri['lokasi_nama']) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Pilih Lokasi di Peta</label>
                        <input type="hidden" id="latitude" name="latitude" value="<?= $entri['latitude'] ?>">
                        <input type="hidden" id="longitude" name="longitude" value="<?= $entri['longitude'] ?>">
                        <div id="map-picker" style="height: 300px;"></div>
                    </div>
                    <div class="col-12">
                        <label for="deskripsi" class="form-label">Cerita Perjalanan</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="6"><?= htmlspecialchars($entri['deskripsi']) ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Kategori</label>
                        <div class="d-flex flex-wrap gap-2">
                            <input type="checkbox" class="btn-check" id="kat_kuliner" name="kategori[]" value="Kuliner" <?= in_array('Kuliner', $kategori_tersimpan) ? 'checked' : '' ?>>
                            <label class="btn btn-outline-secondary" for="kat_kuliner">🍽️ Kuliner</label>

                            <input type="checkbox" class="btn-check" id="kat_petualangan" name="kategori[]" value="Petualangan" <?= in_array('Petualangan', $kategori_tersimpan) ? 'checked' : '' ?>>
                            <label class="btn btn-outline-secondary" for="kat_petualangan">⛰️ Petualangan</label>

                            <input type="checkbox" class="btn-check" id="kat_romantis" name="kategori[]" value="Romantis" <?= in_array('Romantis', $kategori_tersimpan) ? 'checked' : '' ?>>
                            <label class="btn btn-outline-secondary" for="kat_romantis">❤️ Romantis</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="foto" class="form-label">Unggah Foto Baru (Opsional)</label>
                        <input type="file" class="form-control" id="foto" name="foto[]" multiple>
                        <div class="form-text">Kosongkan jika tidak ingin menambah foto. Foto lama tidak akan terhapus.</div>
                    </div>
                    <div class="col-12 text-end">
                        <a href="entri.php?id=<?= $entri['id'] ?>" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary btn-lg">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<!-- Script untuk peta -->
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var initialLat = <?= $entri['latitude'] ?>;
        var initialLng = <?= $entri['longitude'] ?>;
        var map = L.map('map-picker').setView([initialLat, initialLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        // Tampilkan marker di posisi awal
        var marker = L.marker([initialLat, initialLng]).addTo(map);

        function updateMarkerAndFields(latlng) {
            document.getElementById('latitude').value = latlng.lat.toFixed(8);
            document.getElementById('longitude').value = latlng.lng.toFixed(8);
            if (marker) {
                marker.setLatLng(latlng);
            } else {
                marker = L.marker(latlng).addTo(map);
            }
            map.setView(latlng, 15);
        }
        map.on('click', function(e) {
            updateMarkerAndFields(e.latlng);
        });
        var geocoder = L.Control.geocoder({
            defaultMarkGeocode: false
        }).on('markgeocode', function(e) {
            updateMarkerAndFields(e.geocode.center);
        }).addTo(map);
    });
</script>

<?php include 'templates/footer.php'; ?>