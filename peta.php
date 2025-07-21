<?php
require 'database/connection.php';
$stmt = $conn->query("SELECT id, judul, lokasi_nama, latitude, longitude, foto_utama FROM entri");
$lokasi_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
$lokasi_json = json_encode($lokasi_list);

// Cek apakah json_encode berhasil atau ada error
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON Encode Error: " . json_last_error_msg());
}
?>

<?php include 'templates/header.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="content-header">
    <h2 class="greeting"><i class="bi bi-map-fill me-2"></i> Global Travel Map</h2>
    <p class="text-secondary">Explore all your memories visually in one interactive map.</p>
</div>

<div class="map-container-card">
    <div id="map" style="height: 55vh;"></div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var lokasiData = JSON.parse('<?= addslashes($lokasi_json) ?>');

        var map = L.map('map').setView([-2.548926, 118.0148634], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        if (lokasiData && lokasiData.length > 0) {
            lokasiData.forEach(function(lokasi) {
                if (lokasi.latitude != null && lokasi.longitude != null && !isNaN(lokasi.latitude) && !isNaN(lokasi.longitude)) {
                    var lat = parseFloat(lokasi.latitude);
                    var lng = parseFloat(lokasi.longitude);

                    var popupContent =
                        `<div class="text-center" style="width: 180px; color: #333; padding: 10px; border-radius: 5px;">
                        <img src="assets/uploads/${lokasi.foto_utama}" class="img-fluid rounded mb-2" style="max-height: 100px; object-fit: cover;" alt="${lokasi.judul}">
                        <h6 class="mb-2" style="font-size: 1em; font-weight: bold;">${lokasi.judul}</h6>
                        <a href="entri.php?id=${lokasi.id}" class="btn btn-sm" style="background-color: #333; color: #fff; border-radius: 3px; padding: 5px 10px; text-decoration: none;">View</a>
                        </div>`;
                    L.marker([lat, lng]).addTo(map).bindPopup(popupContent);
                }
            });
        }
    });
</script>

<?php include 'templates/footer.php'; ?>