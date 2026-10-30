# 🗺️ Web GIS Pengenalan GADM & Manajemen Depot (Laravel + Leaflet.js)

Aplikasi Web GIS sederhana berbasis **Laravel** dan **Leaflet.js** untuk memvisualisasikan data batas wilayah administratif Indonesia dari **GADM (Global Administrative Areas) Level 0 hingga Level 3**, serta fitur **CRUD Spasial (Create, Read, Update, Delete) Depot** secara interaktif dengan kustomisasi *marker icon*.

Proyek ini dirancang khusus untuk **Modul Praktikum GIS Dasar** tanpa memerlukan koneksi atau instalasi database spasial (PostgreSQL/MySQL), sehingga sangat ringan dan cepat diimplementasikan.

---

## 🌟 Fitur Utama

1. **Visualisasi Batas Wilayah Dinamis (GADM Level 0 – 3):**
   - **Level 0:** Batas Negara (Indonesia)
   - **Level 1:** Batas Provinsi
   - **Level 2:** Batas Kabupaten / Kota
   - **Level 3:** Batas Kecamatan
2. **Auto-Fit Bounds:** Peta secara otomatis menyesuaikan tingkat *zoom* dan pusat tampilan setiap kali layer GeoJSON dimuat.
3. **Manajemen Depot Interaktif (CRUD Spasial):**
   - **Create:** Menambahkan titik depot baru dengan mengeklik area di peta untuk menangkap koordinat Latitude & Longitude secara presisi.
   - **Read:** Menampilkan daftar depot tersimpan dan penanda (*marker*) khusus di atas peta.
   - **Update:** Mengedit nama dan menggeser lokasi depot ke koordinat baru.
   - **Delete:** Menghapus depot dari peta dan daftar.
4. **Zero Database Architecture:** Berjalan sepenuhnya tanpa ketergantungan driver database (Session & Cache menggunakan *File Driver*).

---

## 📁 Struktur Direktori Penting

```text
gis-gadm/
├── app/
│   └── Http/
│       └── Controllers/
│           └── GisController.php      # Controller penyedia View & API GeoJSON
├── routes/
│   └── web.php                        # Routing aplikasi & API dinamis
├── resources/
│   └── views/
│       └── map.blade.php              # Antarmuka Peta (Leaflet.js + Form CRUD Depot)
└── storage/
    └── app/
        └── public/
            └── json/
                └── gadm/              # Direktori penyimpan file GeoJSON GADM
                    ├── gadm41_IDN_0.json
                    ├── gadm41_IDN_1.json
                    ├── gadm41_IDN_2.json
                    └── gadm41_IDN_3.json

```

---

## 🚀 Langkah Instalasi & Konfigurasi

### 1. Clone / Siapkan Proyek Laravel

Buat proyek Laravel baru atau ekstrak repositori ini:

```bash
composer create-project laravel/laravel gis-gadm
cd gis-gadm

```

### 2. Konfigurasi Bebas Database (`.env`)

Buka file `.env` di root proyek, lalu ubah variabel berikut untuk menonaktifkan kebutuhan database SQLite/MySQL:

```env
DB_CONNECTION=null
SESSION_DRIVER=file
CACHE_STORE=file

```

### 3. Siapkan File GeoJSON GADM

Buat direktori penyimpan file di storage Laravel:

```bash
mkdir -p storage/app/public/json/gadm

```

Letakkan file GeoJSON GADM Indonesia v4.1 ke dalam direktori `storage/app/public/json/gadm/` dengan penamaan:

* `gadm41_IDN_0.json`
* `gadm41_IDN_1.json`
* `gadm41_IDN_2.json`
* `gadm41_IDN_3.json`

---

## 💻 Penggunaan Aplikasi

1. Jalankan server lokal Laravel:
```bash
php artisan serve

```


2. Buka browser dan akses alamat:
```text
[http://127.0.0.1:8000](http://127.0.0.1:8000)

```


3. **Pilih Batas Wilayah:** Gunakan *dropdown* di bagian atas untuk mengganti hirarki batas administratif GADM (Level 0 - 3).
4. **Tambah Depot Baru:**
* Klik di sembarang titik lokasi pada peta.
* Koordinat Latitude & Longitude akan terisi otomatis pada form di sebelah kanan.
* Masukkan **Nama Depot**, lalu klik **Simpan Depot**.


5. **Edit / Hapus Depot:**
* Klik tombol **Edit** pada daftar depot di panel kanan untuk memindahkan koordinat atau merubah nama depot.
* Klik tombol **Hapus** untuk menghapus depot dari peta.



---

## 🛠️ Stack Teknologi

* **Backend:** PHP 8.x / Laravel 11+
* **Frontend:** HTML5, CSS3, JavaScript (ES6)
* **GIS Engine:** Leaflet.js v1.9.4
* **Basemap:** OpenStreetMap
* **Data Standard:** GeoJSON (GADM v4.1)

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan modul praktikum akademis dan bersifat open-source.
