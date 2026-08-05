# Script inisialisasi Laravel + Docker

Write-Host "Mengunduh dan memasang Laravel terbaru menggunakan Docker..." -ForegroundColor Cyan

# Check if docker is available
$dockerStatus = docker info 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Docker Desktop belum berjalan. Silakan buka Docker Desktop terlebih dahulu lalu jalankan kembali script ini." -ForegroundColor Red
    exit 1
}

# Run Composer container to install Laravel in current directory
Write-Host "1. Membuat proyek Laravel baru via Composer..." -ForegroundColor Yellow
docker run --rm -v "${PWD}:/app" -w /app composer create-project laravel/laravel . --prefer-dist

# Copy .env.example to .env if .env doesn't exist
if (Test-Path ".env.example") {
    Write-Host "2. Mengonfigurasi file .env..." -ForegroundColor Yellow
    Copy-Item .env.example .env -Force
    
    # Update DB config in .env for Docker
    (Get-Content .env) -replace 'DB_HOST=127.0.0.1', 'DB_HOST=db' `
                       -replace 'DB_PORT=3306', 'DB_PORT=3306' `
                       -replace 'DB_DATABASE=laravel', 'DB_DATABASE=pemetaan' `
                       -replace 'DB_USERNAME=root', 'DB_USERNAME=laravel' `
                       -replace 'DB_PASSWORD=', 'DB_PASSWORD=secret' | Set-Content .env
}

# Build and start docker containers
Write-Host "3. Membangun dan menjalankan Docker container..." -ForegroundColor Yellow
docker compose up -d --build

# Generate application key & run migrations inside app container
Write-Host "4. Generate app key & jalankan migrasi database..." -ForegroundColor Yellow
Start-Sleep -Seconds 5
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate

Write-Host "==========================================" -ForegroundColor Green
Write-Host "BERHASIL! Laravel telah siap dipakai:" -ForegroundColor Green
Write-Host " - Aplikasi Web  : http://localhost:8000" -ForegroundColor Cyan
Write-Host " - phpMyAdmin    : http://localhost:8080" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Green
