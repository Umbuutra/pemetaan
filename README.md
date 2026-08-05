# Aplikasi Pemetaan - Laravel & Docker Setup

Aplikasi web **Pemetaan** dibangun menggunakan **Laravel 13 (PHP 8.4)** dengan arsitektur containerization **Docker** yang mencakup Web Server Nginx, Database MySQL 8.0, dan phpMyAdmin.

---

## 🚀 Fitur & Layanan Docker

| Layanan         | Container Name        | Port Host                                      | Deskripsi                           |
| :-------------- | :-------------------- | :--------------------------------------------- | :---------------------------------- |
| **Laravel App** | `pemetaan-app`        | Internal (`9000`)                              | PHP 8.4 FPM dengan ekstensi Laravel |
| **Nginx Web**   | `pemetaan-web`        | [http://localhost:8000](http://localhost:8000) | Reverse Proxy & Web Server          |
| **MySQL DB**    | `pemetaan-db`         | `3306`                                         | MySQL 8.0 Database Server           |
| **phpMyAdmin**  | `pemetaan-phpmyadmin` | [http://localhost:8080](http://localhost:8080) | Interface Manajemen Database MySQL  |

---

## 🛠️ Syarat Sistem

- **Docker Desktop** (Pastikan statusnya _Running_)
- **PowerShell** / Terminal

---

## ⚙️ Cara Menjalankan Aplikasi

### 1. Menjalankan Docker Containers

Jalankan perintah berikut untuk mengaktifkan semua layanan:

```bash
docker compose up -d
```

### 2. Konfigurasi Environment (`.env`)

Pastikan variabel database pada file `.env` mengarah ke container MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=pemetaan
DB_USERNAME=laravel
DB_PASSWORD=12345678
```

### 3. Generate Application Key & Migrasi Database

```bash
# Generate key aplikasi Laravel
docker compose exec app php artisan key:generate

# Jalankan migrasi database
docker compose exec app php artisan migrate
```

---

## 📍 Akses Layanan

- 🌐 **Aplikasi Web**: [http://localhost:8000](http://localhost:8000)
- 🗄️ **phpMyAdmin**: [http://localhost:8080](http://localhost:8080)
    - **Server**: `db`
    - **Username**: `laravel` _(atau `root`)_
    - **Password**: `12345678`

---

## 📝 Perintah Docker & Artisan yang Berguna

| Perintah                                        | Deskripsi                                        |
| :---------------------------------------------- | :----------------------------------------------- |
| `docker compose up -d`                          | Menjalankan semua container di latar belakang    |
| `docker compose down`                           | Menghentikan dan menghapus container             |
| `docker compose ps`                             | Memeriksa status container yang sedang berjalan  |
| `docker compose exec app php artisan <command>` | Menjalankan perintah Artisan di dalam container  |
| `docker compose exec app composer <command>`    | Menjalankan perintah Composer di dalam container |
| `docker compose logs -f`                        | Melihat log sistem container secara real-time    |

---

## 📁 Struktur Direktori Docker

```
pemetaan/
├── docker/
│   ├── nginx/
│   │   └── default.conf       # Konfigurasi Nginx Web Server
│   └── php/
│       └── Dockerfile         # Dockerfile PHP 8.4-FPM Alpine
├── docker-compose.yml         # Berkas Orchestration Docker Services
├── init-laravel.ps1           # Script Otomatisasi Instalasi Windows
└── .env                       # Environment Configuration
```

-+
