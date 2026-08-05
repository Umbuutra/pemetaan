# 🗺️ Roadmap Development: Aplikasi Pemetaan Sebaran Mahasiswa Berdasarkan Wilayah & Akademik

Dokumen ini berisi panduan alur pengembangan (*roadmap*) spesifik untuk **Aplikasi Pemetaan Sebaran Geografis Mahasiswa** berdasarkan hirarki **Wilayah Administratif Indonesia** (`Provinsi` ➔ `Kabupaten/Kota` ➔ `Kecamatan` ➔ `Kelurahan/Desa`) dan **Struktur Akademik Kampus** (`Fakultas` ➔ `Program Studi`).

---

## 🎯 Tujuan Utama Sistem
1. **Pemetaan Sebaran Bertingkat**: Menampilkan sebaran tempat tinggal / asal mahasiswa dari tingkat Provinsi hingga Kelurahan/Desa secara akurat.
2. **Visualisasi GIS Interaktif**: Menampilkan peta wilayah dengan marker clustering, choropleth map, dan statistik per daerah.
3. **Analisis Demografi Akademik**: Menganalisis daerah penyumbang mahasiswa terbanyak per Fakultas & Program Studi untuk keperluan strategi promosi penerimaan mahasiswa baru (PMB).
4. **Hierarki Data Lengkap**: Mengintegrasikan 8 entitas utama: `users`, `fakultas`, `program_studi`, `provinsi`, `kabupaten`, `kecamatan`, `kelurahan`, dan `mahasiswa`.

---

## 🗄️ Entitas & Relasi Data Utama (8 Tabel Utama)

```mermaid
erDiagram
    users ||--o| mahasiswa : "1 to 1"
    fakultas ||--o{ program_studi : "1 to Many"
    program_studi ||--o{ mahasiswa : "1 to Many"
    provinsi ||--o{ kabupaten : "1 to Many"
    kabupaten ||--o{ kecamatan : "1 to Many"
    kecamatan ||--o{ kelurahan : "1 to Many"
    kelurahan ||--o{ mahasiswa : "1 to Many"

    users {
        bigint id PK
        string name
        string email UK
        enum role "superadmin, admin_fakultas, admin_prodi, mahasiswa"
        string password
    }

    fakultas {
        bigint id PK
        string kode_fakultas UK
        string nama_fakultas
    }

    program_studi {
        bigint id PK
        bigint fakultas_id FK
        string kode_prodi UK
        string nama_prodi
        enum jenjang "D3, D4, S1, S2, S3"
    }

    provinsi {
        bigint id PK
        string kode_provinsi UK
        string nama_provinsi
        decimal latitude
        decimal longitude
    }

    kabupaten {
        bigint id PK
        bigint provinsi_id FK
        string kode_kabupaten UK
        string nama_kabupaten
        enum jenis "kabupaten, kota"
        decimal latitude
        decimal longitude
    }

    kecamatan {
        bigint id PK
        bigint kabupaten_id FK
        string kode_kecamatan UK
        string nama_kecamatan
        decimal latitude
        decimal longitude
    }

    kelurahan {
        bigint id PK
        bigint kecamatan_id FK
        string kode_kelurahan UK
        string nama_kelurahan
        string kode_pos
        decimal latitude
        decimal longitude
    }

    mahasiswa {
        bigint id PK
        bigint user_id FK
        bigint program_studi_id FK
        bigint kelurahan_id FK
        string nim UK
        string nama_lengkap
        year angkatan
        enum status_kuliah "aktif, lulus, cuti, do"
        text alamat_detail
        decimal latitude "Titik Presisi GIS"
        decimal longitude "Titik Presisi GIS"
    }
```

---

## 🗓️ Tahapan Pengembangan (Phase-by-Phase Roadmap)

```mermaid
gantt
    title Roadmap Pemetaan Sebaran Mahasiswa (Wilayah & Akademik)
    dateFormat  YYYY-MM-DD
    section Fase 1: Perancangan Schema
    Migrasi Tabel Wilayah 4-Tingkat       :active, f1, 2026-08-05, 5d
    Migrasi Tabel Akademik & Mahasiswa    :f2, 2026-08-08, 5d
    section Fase 2: Master Data & Seeding
    Import Wilayah BPS/Kemendagri (38 Prov):f3, 2026-08-12, 5d
    Seeder Fakultas & Program Studi        :f4, 2026-08-15, 3d
    section Fase 3: User & Dynamic Form
    Dependent Dropdown Alamat (Prov->Kel)  :f5, 2026-08-18, 7d
    Form Profil & Pinpoint Lokasi Peta     :f6, 2026-08-23, 7d
    section Fase 4: GIS & Analytics
    Visualisasi Peta Sebaran (Leaflet.js)  :f7, 2026-08-28, 10d
    Dashboard Demografi Per Fakultas/Prodi :f8, 2026-09-05, 7d
    section Fase 5: Reporting & Deploy
    Export Data Sebaran (Excel/PDF)        :f9, 2026-09-12, 5d
    Deployment Docker Production           :f10, 2026-09-15, 3d
```

---

### 🔹 Fase 1: Perancangan Basis Data (Database Design)
- [ ] **Struktur Master Akademik**:
  - `fakultas`: Mengelompokkan program studi di bawah fakultas tertentu.
  - `program_studi`: Relasi `fakultas_id`, kode prodi, nama prodi, dan jenjang.
- [ ] **Struktur Hirarki Wilayah Indonesia**:
  - `provinsi`: Kode ISO/Kemendagri, Nama Provinsi, Pusat Koordinat GIS.
  - `kabupaten`: Relasi `provinsi_id`, Kode Kabupaten/Kota, Jenis (Kabupaten / Kota).
  - `kecamatan`: Relasi `kabupaten_id`, Kode Kecamatan.
  - `kelurahan`: Relasi `kecamatan_id`, Kode Kelurahan, Kode Pos.
- [ ] **Struktur Mahasiswa & Users**:
  - `users`: Role (`superadmin`, `admin_fakultas`, `admin_prodi`, `mahasiswa`).
  - `mahasiswa`: Memiliki Foreign Key `program_studi_id` dan `kelurahan_id` serta titik koordinat presisi (`latitude`, `longitude`).

---

### 🔹 Fase 2: Impor Data Master & Seeding
- [ ] **Data Wilayah Wilayah Indonesia (Kemendagri / BPS)**:
  - Mengimpor 38 Provinsi, ~514 Kabupaten/Kota, ~7.200 Kecamatan, dan ~83.000 Kelurahan/Desa.
- [ ] **Data Fakultas & Program Studi**:
  - Menyediakan seeder Fakultas (Teknik, Ilmu Komputer, Ekonomi, dll.) dan Program Studi (Teknik Informatika, Sistem Informasi, Manajemen, dll.).

---

### 🔹 Fase 3: Portal Input Data Mandiri & Dependent Dropdown
- [ ] **Form Input Alamat Berjenjang (*Dependent Dropdown*)**:
  - Fitur AJAX / API saat pilih **Provinsi** ➔ otomatis memuat **Kabupaten/Kota** ➔ memuat **Kecamatan** ➔ memuat **Kelurahan**.
- [ ] **Fitur Pinpoint Lokasi Tempat Tinggal**:
  - Peta kecil di form profil untuk mahasiswa menandai titik lokasi persis tempat tinggal (*Drag & Drop Marker*).

---

### 🔹 Fase 4: GIS Mapping & Analytics Dashboard
- [ ] **Peta Sebaran Mahasiswa (Interactive GIS Map)**:
  - Gunakan **Leaflet.js** / OpenStreetMap dengan **Marker Cluster**.
  - **Filter Interaktif Multi-Dimensi**:
    - Filter berdasarkan **Fakultas** / **Program Studi**.
    - Filter berdasarkan **Angkatan** & **Status Kuliah** (Aktif/Lulus).
    - Zoom-to-Region: Klik Provinsi/Kabupaten otomatis memperbesar peta ke wilayah tersebut.
- [ ] **Dashboard Statistik Demografi**:
  - **Bar Chart**: 10 Kabupaten/Kota penyumbang mahasiswa terbanyak.
  - **Pie Chart**: Persentase Sebaran Mahasiswa per Fakultas.
  - **Tabel Rekapitulasi**: Jumlah mahasiswa per Provinsi & Kabupaten.

---

### 🔹 Fase 5: Pelaporan, Export & Deployment
- [ ] **Export Data Sebaran**:
  - Export rekapitulasi sebaran ke Excel (`.xlsx`) per Fakultas / Per Wilayah.
  - Cetak Ringkasan Laporan Peta ke PDF.
- [ ] **Deployment Docker Container**:
  - Deploy seluruh aplikasi menggunakan Docker & Nginx.
