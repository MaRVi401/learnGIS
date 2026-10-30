<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="https://awsimages.detik.net.id/content/2014/05/30/1562/prabowosubianto.jpeg">
    <title>Pengenalan GIS</title>

    <!-- Leaflet CSS via CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f6f9;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .toolbar {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        select {
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 14px;
            cursor: pointer;
        }

        #map {
            height: 600px;
            width: 100%;
            border-radius: 6px;
        }

        .loading {
            display: none;
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Geographic Information System</h2>

        <div class="toolbar">
            <label for="levelSelect"><b>Pilih Level Wilayah:</b></label>
            <select id="levelSelect" onchange="loadGadmLevel(this.value)">
                <option value="0" selected>Negara</option>
                <option value="1">Provinsi</option>
                <option value="2">Kabupaten / Kota</option>
                <option value="3">Kecamatan</option>
                <option value="4">Desa / Kelurahan</option>
            </select>
            <span id="loadingIndicator" class="loading">Memuat data GeoJSON...</span>
        </div>

        <div id="map"></div>
    </div>

    <!-- Leaflet JS via CDN -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // 1. Inisialisasi Peta (Center Indonesia)
        const map = L.map('map').setView([-2.548926, 118.014863], 5);

        // 2. Basemap OpenStreetMap
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        let currentGeoJsonLayer = null;

        // 3. Fungsi Load GeoJSON dari Endpoint Laravel
        function loadGadmLevel(level) {
            const loading = document.getElementById('loadingIndicator');
            loading.style.display = 'inline';

            if (currentGeoJsonLayer) {
                map.removeLayer(currentGeoJsonLayer);
            }

            fetch(`/api/geojson/gadm/${level}`)
                .then(res => {
                    if (!res.ok) throw new Error(`GeoJSON Level ${level} tidak ditemukan.`);
                    return res.json();
                })
                .then(data => {
                    currentGeoJsonLayer = L.geoJSON(data, {
                        style: {
                            color: '#1d3557',
                            weight: 1.5,
                            fillColor: '#457b9d',
                            fillOpacity: 0.4
                        },
                        onEachFeature: function(feature, layer) {
                            const p = feature.properties || {};

                            const namaWilayah = p.NAME_4 || p.NAME_3 || p.NAME_2 || p.NAME_1 || p.NAME_0 ||
                                'Wilayah';
                            const tipeWilayah = p.TYPE_2 || p.TYPE_1 || '';

                            layer.bindPopup(`
        <div style="font-family: sans-serif;">
            <strong style="font-size: 14px;">${namaWilayah}</strong><br>
            <small style="color: #666;">${tipeWilayah} (GADM Level ${level})</small>
        </div>
    `);
                        }
                    }).addTo(map);

                    map.fitBounds(currentGeoJsonLayer.getBounds());
                })
                .catch(err => {
                    alert(err.message);
                })
                .finally(() => {
                    loading.style.display = 'none';
                });
        }

        // Default Load Level 0 saat pertama kali dibuka
        loadGadmLevel(0);
    </script>

</body>

</html>
