<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Praktikum Web GIS - GADM & CRUD Depot</title>

    <!-- Leaflet CSS CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background-color: #f8f9fa; }
        .card { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .grid-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 15px; }
        .toolbar { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
        select, input, button { padding: 8px 12px; border-radius: 4px; border: 1px solid #ced4da; font-size: 14px; }
        #map { height: 550px; width: 100%; border-radius: 6px; border: 1px solid #dee2e6; }
        .form-panel { background: #f1f3f5; padding: 15px; border-radius: 6px; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 4px; font-size: 13px; }
        .form-group input { width: 100%; box-sizing: border-box; }
        .btn-primary { background-color: #0d6efd; color: white; border: none; cursor: pointer; width: 100%; font-weight: 600; }
        .btn-cancel { background-color: #6c757d; color: white; border: none; cursor: pointer; width: 100%; margin-top: 5px; display: none; }
        .depot-list { margin-top: 15px; max-height: 200px; overflow-y: auto; }
        .depot-item { background: white; padding: 8px; border-radius: 4px; border: 1px solid #dee2e6; margin-bottom: 6px; font-size: 13px; display: flex; justify-content: space-between; align-items: center; }
        .loading-text { display: none; color: #0d6efd; font-weight: 600; font-size: 14px; }
    </style>
</head>
<body>

<div class="card">
    <h2>Peta Interaktif GADM & Manajemen Depot (CRUD Spasial)</h2>
    <p>Modul Praktikum Web GIS Tanpa Database menggunakan Laravel & Leaflet.js</p>

    <div class="toolbar">
        <label for="levelSelect"><b>Batas Wilayah GADM:</b></label>
        <select id="levelSelect" onchange="loadGadmLevel(this.value)">
            <option value="0">Level 0 - Batas Negara</option>
            <option value="1">Level 1 - Batas Provinsi</option>
            <option value="2" selected>Level 2 - Batas Kabupaten / Kota</option>
            <option value="3">Level 3 - Batas Kecamatan</option>
        </select>
        <span id="loadingIndicator" class="loading-text">Memuat GeoJSON...</span>
    </div>

    <div class="grid-layout">
        <!-- Peta Utama -->
        <div id="map"></div>

        <!-- Form CRUD Depot -->
        <div class="form-panel">
            <h3 id="formTitle">Tambah Depot Baru</h3>
            <p style="font-size: 12px; color: #6c757d;">Klik area pada peta untuk mengisi koordinat secara otomatis.</p>
            
            <form id="depotForm" onsubmit="saveDepot(event)">
                <input type="hidden" id="depotId">
                <div class="form-group">
                    <label for="depotName">Nama Depot</label>
                    <input type="text" id="depotName" placeholder="Contoh: Depot Utama Subang" required>
                </div>
                <div class="form-group">
                    <label for="latitude">Latitude</label>
                    <input type="text" id="latitude" readonly required>
                </div>
                <div class="form-group">
                    <label for="longitude">Longitude</label>
                    <input type="text" id="longitude" readonly required>
                </div>
                <button type="submit" class="btn-primary" id="btnSubmit">Simpan Depot</button>
                <button type="button" class="btn-cancel" id="btnCancel" onclick="resetForm()">Batal Edit</button>
            </form>

            <h4 style="margin-top: 15px; margin-bottom: 8px;">Daftar Depot</h4>
            <div class="depot-list" id="depotListContainer"></div>
        </div>
    </div>
</div>

<!-- Leaflet JS CDN -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // 1. Inisialisasi Peta
    const map = L.map('map').setView([-6.875, 107.608], 10);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let currentGeoJsonLayer = null;
    let depotMarkers = {}; // Store layer marker berdasarkan ID Depot
    let depots = []; // Store data depot (In-Memory Array)

    // 2. Definisi Custom Icon untuk Depot
    const depotIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png', // Custom Marker Icon
        iconSize: [36, 36],
        iconAnchor: [18, 36],
        popupAnchor: [0, -32]
    });

    // 3. Load GeoJSON GADM
    function loadGadmLevel(level) {
        const loading = document.getElementById('loadingIndicator');
        loading.style.display = 'inline';

        if (currentGeoJsonLayer) {
            map.removeLayer(currentGeoJsonLayer);
        }

        fetch(`/api/geojson/gadm/${level}`)
            .then(res => {
                if (!res.ok) throw new Error(`Gagal memuat data GeoJSON Level ${level}.`);
                return res.json();
            })
            .then(data => {
                currentGeoJsonLayer = L.geoJSON(data, {
                    style: { color: '#0d6efd', weight: 1.5, fillColor: '#0d6efd', fillOpacity: 0.15 },
                    onEachFeature: function (feature, layer) {
                        const p = feature.properties || {};
                        const namaWilayah = p.NAME_3 || p.NAME_2 || p.NAME_1 || p.NAME_0 || 'Wilayah';
                        layer.bindPopup(`<b>${namaWilayah}</b> (GADM Level ${level})`);
                    }
                }).addTo(map);

                map.fitBounds(currentGeoJsonLayer.getBounds());
            })
            .catch(err => alert(err.message))
            .finally(() => loading.style.display = 'none');
    }

    // 4. Capture Event Klik di Peta untuk Input Koordinat Form
    map.on('click', function (e) {
        document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
        document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
    });

    // 5. CRUD Depot: Simpan / Update Data
    function saveDepot(event) {
        event.preventDefault();
        const id = document.getElementById('depotId').value;
        const name = document.getElementById('depotName').value;
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);

        if (id) {
            // Logic Update (Edit)
            const index = depots.findIndex(d => d.id == id);
            if (index !== -1) {
                depots[index] = { id: parseInt(id), name, lat, lng };
                updateMarkerOnMap(depots[index]);
            }
        } else {
            // Logic Create (Tambah Baru)
            const newDepot = { id: Date.now(), name, lat, lng };
            depots.push(newDepot);
            addMarkerToMap(newDepot);
        }

        resetForm();
        renderDepotList();
    }

    // 6. Menambahkan Marker ke Peta
    function addMarkerToMap(depot) {
        const marker = L.marker([depot.lat, depot.lng], { icon: depotIcon }).addTo(map);
        marker.bindPopup(`<b>🏢 ${depot.name}</b><br>Lat: ${depot.lat}<br>Lng: ${depot.lng}`);
        depotMarkers[depot.id] = marker;
    }

    // 7. Update Posisi/Popup Marker di Peta
    function updateMarkerOnMap(depot) {
        if (depotMarkers[depot.id]) {
            depotMarkers[depot.id].setLatLng([depot.lat, depot.lng]);
            depotMarkers[depot.id].setPopupContent(`<b>🏢 ${depot.name}</b><br>Lat: ${depot.lat}<br>Lng: ${depot.lng}`);
        }
    }

    // 8. Prepare Form untuk Edit
    function editDepot(id) {
        const depot = depots.find(d => d.id === id);
        if (depot) {
            document.getElementById('depotId').value = depot.id;
            document.getElementById('depotName').value = depot.name;
            document.getElementById('latitude').value = depot.lat;
            document.getElementById('longitude').value = depot.lng;
            
            document.getElementById('formTitle').innerText = 'Edit Depot';
            document.getElementById('btnSubmit').innerText = 'Update Depot';
            document.getElementById('btnCancel').style.display = 'block';

            map.panTo([depot.lat, depot.lng]);
            depotMarkers[depot.id].openPopup();
        }
    }

    // 9. Hapus Depot
    function deleteDepot(id) {
        if (confirm('Apakah Anda yakin ingin menghapus depot ini?')) {
            depots = depots.filter(d => d.id !== id);
            if (depotMarkers[id]) {
                map.removeLayer(depotMarkers[id]);
                delete depotMarkers[id];
            }
            renderDepotList();
        }
    }

    // 10. Reset Form State
    function resetForm() {
        document.getElementById('depotId').value = '';
        document.getElementById('depotName').value = '';
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        document.getElementById('formTitle').innerText = 'Tambah Depot Baru';
        document.getElementById('btnSubmit').innerText = 'Simpan Depot';
        document.getElementById('btnCancel').style.display = 'none';
    }

    // 11. Render Daftar Depot di Sidebar
    function renderDepotList() {
        const container = document.getElementById('depotListContainer');
        container.innerHTML = '';

        if (depots.length === 0) {
            container.innerHTML = '<p style="font-size: 12px; color: #888;">Belum ada depot tersimpan.</p>';
            return;
        }

        depots.forEach(depot => {
            const item = document.createElement('div');
            item.className = 'depot-item';
            item.innerHTML = `
                <div>
                    <strong>${depot.name}</strong><br>
                    <small style="color: #666;">${depot.lat}, ${depot.lng}</small>
                </div>
                <div>
                    <button onclick="editDepot(${depot.id})" style="background:#ffc107; border:none; padding:4px 8px; border-radius:3px; cursor:pointer;">Edit</button>
                    <button onclick="deleteDepot(${depot.id})" style="background:#dc3545; color:white; border:none; padding:4px 8px; border-radius:3px; cursor:pointer;">Hapus</button>
                </div>
            `;
            container.appendChild(item);
        });
    }

    // Initial Load
    const defaultLevel = document.getElementById('levelSelect').value;
    loadGadmLevel(defaultLevel);
    renderDepotList();
</script>

</body>
</html>