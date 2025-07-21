<?php
require 'database/connection.php';
$stmt = $conn->query("SELECT id, judul, lokasi_nama, latitude, longitude, foto_utama FROM entri");
$lokasi_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
$lokasi_json = json_encode($lokasi_list);
?>

<?php include 'templates/header.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<div class="content-header mb-4">
    <h2 class="greeting"><i class="bi bi-map-fill me-2"></i> Peta Global Perjalanan</h2>
    <p class="text-secondary">Jelajahi semua kenangan Anda secara visual dalam satu peta interaktif.</p>
</div>

<div class="map-container-card">
    <div id="map" style="height: 65vh;"></div>
</div>

<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map').setView([-2.548926, 118.0148634], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.Control.geocoder({
            defaultMarkGeocode: false
        }).on('markgeocode', function(e) {
            map.fitBounds(e.geocode.bbox);
        }).addTo(map);
        var lokasiData = <?= $lokasi_json ?>;
        lokasiData.forEach(function(lokasi) {
            var marker = L.marker([lokasi.latitude, lokasi.longitude]).addTo(map);
            var popupContent = `<div class="text-center" style="width: 160px;"><img src="assets/uploads/${lokasi.foto_utama}" class="img-fluid rounded mb-2" alt="${lokasi.judul}"><h6 class="mb-1">${lokasi.judul}</h6><a href="entri.php?id=${lokasi.id}" class="btn btn-sm btn-primary">Lihat Cerita</a></div>`;
            marker.bindPopup(popupContent);
        });
    });
</script>

<?php include 'templates/footer.php'; ?>