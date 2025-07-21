<?php
session_start();
require 'database/connection.php';

// Check if ID exists, if not, redirect to index
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM entri WHERE id = ?");
$stmt->execute([$id]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

// If entry with the given ID does not exist
if (!$entry) {
    echo "Entry not found.";
    exit();
}

// Convert category string to array for checkbox matching
$saved_categories = explode(',', $entry['kategori']);
?>

<?php include 'templates/header.php'; ?>

<!-- Add CSS for Geocoder Plugin -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<div class="page-content-wrapper">
    <main class="container-fluid p-4 p-md-5">
        <div class="content-header mb-4">
            <h2 class="greeting"><i class="bi bi-pencil-square me-2"></i> Edit Memory</h2>
            <p class="text-secondary">Update your adventure details.</p>
        </div>

        <div class="form-container-card">
            <form action="proses_edit.php" method="POST" enctype="multipart/form-data">
                <!-- Send entry ID as hidden -->
                <input type="hidden" name="id" value="<?= $entry['id'] ?>">

                <div class="row g-4">
                    <div class="col-12">
                        <label for="judul" class="form-label">Trip Title</label>
                        <input type="text" class="form-control form-control-lg" id="judul" name="judul" value="<?= htmlspecialchars($entry['judul']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_kunjungan" class="form-label">Visit Date</label>
                        <input type="date" class="form-control" id="tanggal_kunjungan" name="tanggal_kunjungan" value="<?= $entry['tanggal_kunjungan'] ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="rating" class="form-label">Experience Rating (1-5)</label>
                        <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" value="<?= $entry['rating'] ?>" required>
                    </div>
                    <div class="col-12">
                        <label for="lokasi_nama" class="form-label">Location Name</label>
                        <input type="text" class="form-control" id="lokasi_nama" name="lokasi_nama" value="<?= htmlspecialchars($entry['lokasi_nama']) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Pick Location on Map</label>
                        <input type="hidden" id="latitude" name="latitude" value="<?= $entry['latitude'] ?>">
                        <input type="hidden" id="longitude" name="longitude" value="<?= $entry['longitude'] ?>">
                        <div id="map-picker" style="height: 300px;"></div>
                    </div>
                    <div class="col-12">
                        <label for="deskripsi" class="form-label">Trip Story</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="6"><?= htmlspecialchars($entry['deskripsi']) ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Category</label>
                        <div class="d-flex flex-wrap gap-2">
                            <input type="checkbox" class="btn-check" id="kat_kuliner" name="kategori[]" value="Kuliner" <?= in_array('Kuliner', $saved_categories) ? 'checked' : '' ?>>
                            <label class="btn btn-outline-secondary" for="kat_kuliner">🍽️ Culinary</label>

                            <input type="checkbox" class="btn-check" id="kat_petualangan" name="kategori[]" value="Petualangan" <?= in_array('Petualangan', $saved_categories) ? 'checked' : '' ?>>
                            <label class="btn btn-outline-secondary" for="kat_petualangan">⛰️ Adventure</label>

                            <input type="checkbox" class="btn-check" id="kat_romantis" name="kategori[]" value="Romantis" <?= in_array('Romantis', $saved_categories) ? 'checked' : '' ?>>
                            <label class="btn btn-outline-secondary" for="kat_romantis">❤️ Romantic</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="foto" class="form-label">Upload New Photo (Optional)</label>
                        <input type="file" class="form-control" id="foto" name="foto[]" multiple>
                        <div class="form-text">Leave empty if you don't want to add photos. Old photos will not be deleted.</div>
                    </div>
                    <div class="col-12 text-end">
                        <a href="entri.php?id=<?= $entry['id'] ?>" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<!-- Script for map -->
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var initialLat = <?= $entry['latitude'] ?>;
        var initialLng = <?= $entry['longitude'] ?>;
        var map = L.map('map-picker').setView([initialLat, initialLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        // Show marker at initial position
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