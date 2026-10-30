# 🗺️ Web GIS GADM & CRUD Depot (Laravel + Leaflet.js)

Dokumentasi resmi dan panduan implementasi untuk proyek **Modul Praktikum: Pengenalan Web GIS dengan Laravel & Leaflet.js (Tanpa Database)**.

Aplikasi ini dirancang untuk memvisualisasikan data spasial berformat GeoJSON batas wilayah administratif Indonesia (GADM Level 0 hingga Level 4) serta mengelola lokasi **Depot** secara interaktif menggunakan penanda icon dinamis dan koordinat Latitude/Longitude desimal.

---

## 📌 Informasi Praktikum

* **Mata Kuliah / Pelatihan:** Pemrograman Web / Sistem Informasi Geografis (GIS)
* **Tingkat:** Intermediate
* **Prasyarat:** Pemrograman Web Dasar & Laravel Basic
* **Arsitektur:** *Zero-Database Architecture* (Pengolahan data spasial berbasis file lokal & *client-side state*)

---

## 🚀 Fitur Utama

1. **GADM Boundary Visualizer (Level 0 – 4):**
   * **Level 0:** Batas Negara (Indonesia)
   * **Level 1:** Batas Provinsi
   * **Level 2:** Batas Kabupaten / Kota
   * **Level 3:** Batas Kecamatan
   * **Level 4:** Batas Desa / Kelurahan
2. **Auto Fit Bounds & Interactive Popups:** Pandangan peta (*zoom & center*) otomatis menyesuaikan dengan batas wilayah GeoJSON yang dimuat, disertai pop-up informasi atribut nama & tipe wilayah.
3. **CRUD Depot Interaktif (Tugas Evaluasi):**
   * **Create:** Input titik depot baru via klik langsung di peta atau pengetikan koordinat desimal.
   * **Read:** Menampilkan marker icon depot khusus di atas peta dan daftar depot di sidebar.
   * **Update:** Mengedit nama dan memindahkan posisi koordinat depot secara real-time.
   * **Delete:** Menghapus marker depot dari peta dan daftar.

---

## 🛠️ Stack Teknologi

* **Backend Framework:** Laravel 11+ (PHP 8.2+)
* **Frontend Engine:** Blade Engine, HTML5, CSS3, JavaScript (ES6 Native)
* **Mapping Library:** Leaflet.js v1.9.4 (via CDN)
* **Tile Server:** OpenStreetMap
* **Dataset Spasial:** GADM v4.1 (Global Administrative Areas)

---

## 📁 Struktur File & Direktori Utama

```text
gis-gadm/
├── app/
│   └── Http/
│       └── Controllers/
│           └── GisController.php      # Controller penyedia View & API GeoJSON GADM
├── routes/
│   └── web.php                        # Deklarasi Route Web & Endpoint API
├── resources/
│   └── views/
│       └── map.blade.php              # Blade View (Antarmuka Peta, Leaflet & Form CRUD)
└── storage/
    └── app/
        └── public/
            └── json/
                └── gadm/              # Folder Penyimpanan File GeoJSON GADM v4.1
                    ├── gadm41_IDN_0.json
                    ├── gadm41_IDN_1.json
                    ├── gadm41_IDN_2.json
                    ├── gadm41_IDN_3.json
                    └── gadm41_IDN_4.json
```

---

## ⚙️ Petunjuk Setup & Instalasi

### 1. Inisialisasi Proyek
```bash
composer create-project laravel/laravel gis-gadm
cd gis-gadm
```

### 2. Konfigurasi Lingkungan Tanpa Database (`.env`)
Buka file `.env` di root proyek dan pastikan driver database dan session dikonfigurasi ke opsi *file-based* untuk mencegah error PDO/SQLite:
```env
DB_CONNECTION=null
SESSION_DRIVER=file
CACHE_STORE=file
```

### 3. Penyiapan Direktori GeoJSON
Buat folder penyimpanan di dalam direktori storage Laravel:
```bash
mkdir -p storage/app/public/json/gadm
```
Letakkan file dataset GADM v4.1 ke dalam direktori `storage/app/public/json/gadm/` dengan penamaan standar:
* `gadm41_IDN_0.json`
* `gadm41_IDN_1.json`
* `gadm41_IDN_2.json`
* `gadm41_IDN_3.json`
* `gadm41_IDN_4.json`

---

## 🔌 API Reference Endpoint

### Fetch GADM GeoJSON Data
* **URL:** `/api/geojson/gadm/{level}`
* **Method:** `GET`
* **Url Params:** `level` = `[0-4]`
* **Success Response:** `200 OK` (Application/JSON GeoJSON FeatureCollection)
* **Error Response:** `400 Bad Request` / `404 Not Found`

---

## 📝 Format Koordinat Input Depot

Sistem menerima input koordinat spasial menggunakan format **Decimal Degrees (DD)**:
* **Latitude (Lintang):** Angka desimal (contoh: `-6.327583`, tanda negatif menandakan Lintang Selatan / LS).
* **Longitude (Bujur):** Angka desimal (contoh: `108.324936`, angka positif menandakan Bujur Timur / BT).

---

## 💻 Jalankan Aplikasi

1. Jalankan server lokal Laravel:
   ```bash
   php artisan serve
   ```
2. Buka browser dan buka alamat:
   ```text
   http://127.0.0.1:8000
   ```

---

## 📄 Lisensi & Modul
Proyek ini dikembangkan khusus sebagai materi modul praktikum akademik Sistem Informasi Geografis dan Pemrograman Web. Bebas digunakan dan disebarluaskan untuk kepentingan pendidikan.