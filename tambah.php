<?php include 'templates/header.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<div class="content-header mb-4">
    <h2 class="greeting"><i class="bi bi-pencil-square me-2"></i> Add New Memory</h2>
    <p class="text-secondary">A perfectly aligned layout to document your adventure.</p>
</div>

<form action="proses_tambah.php" method="POST" enctype="multipart/form-data">
    <div class="row g-4 align-items-stretch">

        <div class="col-lg-7 d-flex flex-column">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-bookmark-fill me-2 text-dark"></i>Main Story</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Trip Title</label>
                        <input type="text" class="form-control form-control-lg" id="judul" name="judul" placeholder="e.g., Enjoying the Sunset at Tanah Lot" required>
                    </div>
                    <div>
                        <label for="deskripsi" class="form-label">Trip Story</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="8" placeholder="Share all your unforgettable moments here..."></textarea>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm flex-grow-1">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-list-check me-2 text-dark"></i>Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_kunjungan" class="form-label">Visit Date</label>
                        <input type="date" class="form-control" id="tanggal_kunjungan" name="tanggal_kunjungan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Experience Rating</label>
                        <div class="star-rating">
                            <input type="radio" id="5-stars" name="rating" value="5" /><label for="5-stars" class="bi bi-star-fill"></label>
                            <input type="radio" id="4-stars" name="rating" value="4" /><label for="4-stars" class="bi bi-star-fill"></label>
                            <input type="radio" id="3-stars" name="rating" value="3" /><label for="3-stars" class="bi bi-star-fill"></label>
                            <input type="radio" id="2-stars" name="rating" value="2" /><label for="2-stars" class="bi bi-star-fill"></label>
                            <input type="radio" id="1-star" name="rating" value="1" required /><label for="1-star" class="bi bi-star-fill"></label>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Category</label>
                        <div class="d-flex flex-wrap gap-2">
                            <input type="radio" class="btn-check" id="kat_kuliner" name="kategori" value="Place to Eat" required>
                            <label class="btn btn-outline-secondary kategori-btn" for="kat_kuliner"><i class="bi bi-egg-fried text-dark"></i> Place to Eat</label>
                            <input type="radio" class="btn-check" id="kat_petualangan" name="kategori" value="Place to Go" required>
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
                        <input type="text" class="form-control" id="lokasi_nama" name="lokasi_nama" placeholder="e.g., Dieng Plateau, Central Java" required>
                    </div>
                    <div>
                        <label class="form-label">Pin on Map</label>
                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                        <div id="map-picker" style="height: 400px; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm flex-grow-1">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-camera-fill me-2 text-dark"></i>Photo Gallery</h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <label for="foto" class="form-label">Upload Your Photos</label>
                    <input type="file" class="form-control" id="foto" name="foto[]" multiple required>
                    <div class="form-text mt-2">You can select multiple files. The first photo will be the main cover.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 text-end">
        <button type="submit" class="btn btn-dark btn-lg px-5">Save</button>
    </div>
</form>

<?php include 'templates/footer.php'; ?>

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

        // This is the search control. It was correct in your code.
        L.Control.geocoder({
            defaultMarkGeocode: false
        }).on('markgeocode', function(e) {
            updateMarkerAndFields(e.geocode.center);
        }).addTo(map);

        // This is the FIX.
        // It forces the map to re-check its size, ensuring all controls appear correctly.
        setTimeout(function() {
            map.invalidateSize();
        }, 100);
    });
</script>