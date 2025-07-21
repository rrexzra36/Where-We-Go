<?php include 'templates/header.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<div class="content-header mb-4">
    <h2 class="greeting"><i class="bi bi-pencil-square me-2"></i> Add New Memory</h2>
    <p class="text-secondary">Fill in the details of your adventure.</p>
</div>

<div class="form-container-card">
    <form action="proses_tambah.php" method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <div class="col-12">
                <label for="judul" class="form-label">Trip Title</label>
                <input type="text" class="form-control form-control-lg" id="judul" name="judul" placeholder="Example: Enjoying Sunset at Tanah Lot" required>
            </div>
            <div class="col-md-6">
                <label for="tanggal_kunjungan" class="form-label">Visit Date</label>
                <input type="date" class="form-control" id="tanggal_kunjungan" name="tanggal_kunjungan" required>
            </div>
            <div class="col-md-6">
                <label for="rating" class="form-label">Experience Rating (1-5)</label>
                <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
            </div>
            <div class="col-12">
                <label for="lokasi_nama" class="form-label">Location Name</label>
                <input type="text" class="form-control" id="lokasi_nama" name="lokasi_nama" placeholder="Example: Tanah Lot Temple, Bali" required>
            </div>
            <div class="col-12">
                <label class="form-label">Pick Location on Map</label>
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">
                <div id="map-picker" style="height: 300px;"></div>
            </div>
            <div class="col-12">
                <label for="deskripsi" class="form-label">Trip Story</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="6" placeholder="Share all your unforgettable moments here..."></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Category</label>
                <div class="d-flex flex-wrap gap-2">
                    <input type="checkbox" class="btn-check" id="kat_kuliner" name="kategori[]" value="Culinary">
                    <label class="btn btn-outline-secondary" for="kat_kuliner">🍽️ Culinary</label>
                    <input type="checkbox" class="btn-check" id="kat_petualangan" name="kategori[]" value="Adventure">
                    <label class="btn btn-outline-secondary" for="kat_petualangan">⛰️ Adventure</label>
                    <input type="checkbox" class="btn-check" id="kat_romantis" name="kategori[]" value="Romantic">
                    <label class="btn btn-outline-secondary" for="kat_romantis">❤️ Romantic</label>
                </div>
            </div>
            <div class="col-12">
                <label for="foto" class="form-label">Upload Photos (select multiple)</label>
                <input type="file" class="form-control" id="foto" name="foto[]" multiple required>
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary btn-lg">Save Story</button>
            </div>
        </div>
    </form>
</div>

<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map-picker').setView([-6.200000, 106.816666], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        var marker;

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
    });
</script>

<?php include 'templates/footer.php'; ?>