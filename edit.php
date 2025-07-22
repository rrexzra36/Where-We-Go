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
?>

<?php include 'templates/header.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<div class="content-header mb-4">
    <h2 class="greeting"><i class="bi bi-pencil-square me-2"></i> Edit Memory</h2>
    <p class="text-secondary">Update your adventure details.</p>
</div>

<form action="proses_edit.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $entry['id'] ?>">
    <div class="row g-4 align-items-stretch">

        <div class="col-lg-7 d-flex flex-column">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-bookmark-fill me-2 text-dark"></i>Main Story</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Trip Title</label>
                        <input type="text" class="form-control form-control-lg" id="judul" name="judul" value="<?= htmlspecialchars($entry['judul']) ?>" required>
                    </div>
                    <div>
                        <label for="deskripsi" class="form-label">Trip Story</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="8"><?= htmlspecialchars($entry['deskripsi']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm flex-grow-1">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-list-check me-2 text-dark"></i>Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="tanggal_kunjungan" class="form-label">Visit Date</label>
                        <input type="date" class="form-control" id="tanggal_kunjungan" name="tanggal_kunjungan" value="<?= $entry['tanggal_kunjungan'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Experience Rating</label>
                        <div class="star-rating">
                            <input type="radio" id="5-stars" name="rating" value="5" <?= ($entry['rating'] == 5) ? 'checked' : '' ?> /><label for="5-stars" class="bi bi-star-fill"></label>
                            <input type="radio" id="4-stars" name="rating" value="4" <?= ($entry['rating'] == 4) ? 'checked' : '' ?> /><label for="4-stars" class="bi bi-star-fill"></label>
                            <input type="radio" id="3-stars" name="rating" value="3" <?= ($entry['rating'] == 3) ? 'checked' : '' ?> /><label for="3-stars" class="bi bi-star-fill"></label>
                            <input type="radio" id="2-stars" name="rating" value="2" <?= ($entry['rating'] == 2) ? 'checked' : '' ?> /><label for="2-stars" class="bi bi-star-fill"></label>
                            <input type="radio" id="1-star" name="rating" value="1" <?= ($entry['rating'] == 1) ? 'checked' : '' ?> required /><label for="1-star" class="bi bi-star-fill"></label>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Category</label>
                        <div class="d-flex flex-wrap gap-2">
                            <input type="radio" class="btn-check" id="kat_kuliner" name="kategori" value="Place to Eat" <?= ($entry['kategori'] == 'Place to Eat') ? 'checked' : '' ?> required>
                            <label class="btn btn-outline-secondary kategori-btn" for="kat_kuliner"><i class="bi bi-egg-fried text-dark"></i> Place to Eat</label>

                            <input type="radio" class="btn-check" id="kat_petualangan" name="kategori" value="Place to Go" <?= ($entry['kategori'] == 'Place to Go') ? 'checked' : '' ?> required>
                            <label class="btn btn-outline-secondary kategori-btn" for="kat_petualangan"><i class="bi bi-signpost-split-fill text-dark"></i> Place to Go</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 d-flex flex-column">
            <div class="card shadow-sm flex-grow-1 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-geo-alt-fill me-2 text-dark"></i>Location</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="lokasi_nama" class="form-label">Location Name</label>
                        <input type="text" class="form-control" id="lokasi_nama" name="lokasi_nama" value="<?= htmlspecialchars($entry['lokasi_nama']) ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Pin on Map</label>
                        <input type="hidden" id="latitude" name="latitude" value="<?= $entry['latitude'] ?>">
                        <input type="hidden" id="longitude" name="longitude" value="<?= $entry['longitude'] ?>">
                        <div id="map-picker" style="height: 400px; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm flex-grow-1">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-camera-fill me-2 text-dark"></i>Photo Gallery</h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <label for="foto" class="form-label">Upload New Photo (Optional)</label>
                    <input type="file" class="form-control" id="foto" name="foto[]" multiple>
                    <div class="form-text mt-2">Leave empty if you don't want to add new photos. Existing photos will not be replaced.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 text-end">
        <button type="submit" class="btn btn-dark btn-lg px-5">Update</button>
    </div>
</form>

<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var initialLat = <?= $entry['latitude'] ?>;
        var initialLng = <?= $entry['longitude'] ?>;
        var map = L.map('map-picker').setView([initialLat, initialLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

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
        L.Control.geocoder({
            defaultMarkGeocode: false
        }).on('markgeocode', function(e) {
            updateMarkerAndFields(e.geocode.center);
        }).addTo(map);

        setTimeout(function() {
            map.invalidateSize();
        }, 100);
    });
</script>

<?php include 'templates/footer.php'; ?>