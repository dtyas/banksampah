# Bank Sampah - Decoupled Architecture

Project ini sudah dipisah menjadi:

- Backend: Laravel API v1 di `backend/bank-sampah-app`
- Frontend: Vue 3 + Vite SPA di `frontend`

## Deskripsi Project

Bank Sampah adalah sistem informasi pengelolaan sampah berbasis web dengan arsitektur decoupled (backend API + frontend SPA).
Project ini digunakan untuk membantu operasional bank sampah dari proses pendataan nasabah, pengelolaan kategori dan data sampah,
pencatatan transaksi setoran, verifikasi pembayaran/pencairan, sampai rekap laporan.

Alur singkat operasional:

1. Admin/petugas mengelola data master (nasabah, kategori, sampah, user).
2. Setiap setoran dicatat sebagai transaksi dengan detail item sampah, berat, dan subtotal.
3. Sistem menghitung total transaksi dan mengelola proses pembayaran/pencairan saldo.
4. Data operasional direkap dalam dashboard dan laporan untuk monitoring performa.

## Role & Akses

- **Super Admin**: akses penuh semua menu dan operasi.
- **Petugas**: akses dibatasi oleh `menu_access` dan `operational_access`.
- **Nasabah**: akses minimal untuk melihat buku tabungan dan mengajukan pencairan saldo.

Default akses nasabah:

- Menu: Pencairan Saldo
- Operasional: Ajukan Pencairan Saldo

## Fitur Utama

### Fitur Backend (Laravel API)

- Auth Sanctum token-based: login, profile (`me`), logout.
- API versioning (`/api/v1`) dan response JSON seragam (`status`, `message`, `data`).
- Modul data master:
  - Nasabah (terkait user role `nasabah`)
  - Kategori Sampah
  - Sampah
  - User + access management (`menu_access`, `operational_access`)
- Modul transaksi:
  - Transaksi setoran + detail item sampah
  - Pembayaran/pencairan dengan status verifikasi
- Modul nasabah:
  - Buku tabungan (saldo, histori transaksi, histori pencairan)
  - Data rekening/ewallet untuk payout
- Modul laporan:
  - Summary metrik operasional
  - Data chart laporan
  - Rekap transaksi terfilter
- Integrasi Xendit:
  - Payout v2 (`/v2/payouts`)
  - Balance (`/balance`)
  - Webhook payout
- Layered architecture (Controller → Service → Repository) agar kode lebih rapi dan maintainable.

### Fitur Frontend (Vue SPA)

- Single Page Application dengan Vue Router (route-based per menu).
- Halaman admin terpisah per modul:
  - Dashboard
  - Nasabah
  - Kategori Sampah
  - Sampah
  - Transaksi
  - Pembayaran
  - Pencairan Saldo
  - User
  - Laporan
- Halaman nasabah:
  - Buku tabungan (saldo + histori)
  - Ajukan pencairan saldo
- Integrasi API terpusat (Axios interceptor + toast notifikasi success/error).
- State management autentikasi menggunakan Pinia.
- UI modular dengan komponen reusable dan layout shell.

## Arsitektur Backend (Laravel API v1)

Struktur utama:

- `app/Http/Controllers/Api/V1`: Controller versioned API
- `app/Services`: business logic layer
- `app/Repositories/Contracts`: abstraction data access
- `app/Repositories/Eloquent`: implementasi repository berbasis Eloquent
- `app/Http/Resources/V1`: transformer output API

Prinsip implementasi:

- Controller hanya validasi request + panggil Service
- Service menjadi orchestration layer
- Repository mengisolasi query/data access
- Semua response seragam:

```json
{
  "status": true,
  "message": "...",
  "data": {}
}
```

## Sanctum + CORS (Decoupled Folder)

Sanctum sudah dipasang dengan mode token (default untuk SPA terpisah origin).

### Alur token-based (direkomendasikan untuk struktur ini)

1. SPA login ke `POST /api/v1/auth/login`
2. API mengembalikan token personal access
3. SPA simpan token (mis. localStorage)
4. Semua request berikutnya kirim `Authorization: Bearer <token>`

Kelebihan:

- Tidak bergantung cookie lintas origin
- Setup lebih sederhana untuk Docker + Laragon

### Stateful cookie mode (opsional)

Jika ingin session-cookie Sanctum:

- set `SANCTUM_STATEFUL_DOMAINS`
- aktifkan `supports_credentials=true` di CORS
- frontend harus `withCredentials=true`
- panggil `/sanctum/csrf-cookie` sebelum login session

Catatan:

- Untuk domain terpisah frontend/backend, salah konfigurasi CORS + stateful domains adalah sumber error paling umum.
- Jika memakai token mode, kebutuhan ini jauh berkurang.

## Flow Utama

### 1) Setoran Sampah (Admin/Petugas)

1. Admin/petugas input transaksi setoran di modul Transaksi.
2. Sistem menyimpan transaksi dan menghitung total harga setoran.
3. Total harga menambah saldo tabungan nasabah.

### 2) Buku Tabungan Nasabah

1. Nasabah login dan melihat saldo tabungan.
2. Saldo dihitung dari total setoran dikurangi pencairan berhasil dan pencairan pending.

### 3) Pengajuan Pencairan Saldo

1. Nasabah mengajukan pencairan (saldo harus cukup dan rekening/ewallet harus lengkap).
2. Status awal `menunggu`.
3. Admin/petugas melakukan verifikasi (`diverifikasi`).
4. Admin/petugas memproses payout (`diproses`).
5. Xendit mengirim webhook dan status menjadi `berhasil`/`ditolak`.

## Endpoint Auth

- `POST /api/v1/auth/login`
- `GET /api/v1/auth/me` (auth:sanctum)
- `POST /api/v1/auth/logout` (auth:sanctum)

## Endpoint Utama (Ringkas)

- `GET /api/v1/laporan/summary`
- `GET /api/v1/xendit/balance` (admin/petugas)
- `GET /api/v1/nasabah/me/ledger` (nasabah)
- `POST /api/v1/pencairan-saldo/request` (nasabah)
- `POST /api/v1/webhooks/xendit/payouts` (webhook)

## Menjalankan dengan Docker

Jalankan dari root project:

```bash
docker compose up -d --build
```

Inisialisasi backend Laravel di dalam container `php`:

```bash
docker compose exec php sh -lc "cd /var/www/backend/bank-sampah-app && composer install"
docker compose exec php sh -lc "cd /var/www/backend/bank-sampah-app && cp -n .env.example .env || true"
docker compose exec php sh -lc "cd /var/www/backend/bank-sampah-app && php artisan key:generate"
docker compose exec php sh -lc "cd /var/www/backend/bank-sampah-app && php artisan migrate"
docker compose exec php sh -lc "cd /var/www/backend/bank-sampah-app && php artisan db:seed"
```

Service utama:

- `nginx` (Nginx + PHP-FPM 8.3): `http://localhost:8000`
- `mysql` (MySQL 8.4): host `127.0.0.1`, port `3307`
- `node` (Vite dev server): `http://localhost:5173`

Untuk melihat status container:

```bash
docker compose ps
```

## Menjalankan dengan Laragon (Windows)

Jalankan project tanpa Docker, gunakan service Laragon (Apache/Nginx, PHP, MySQL).

Langkah backend Laravel:

```bash
cd backend/bank-sampah-app
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

Konfigurasi database di `backend/bank-sampah-app/.env` untuk Laragon:

- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD` sesuaikan dengan MySQL Laragon

Langkah frontend:

```bash
cd frontend
npm install
npm run dev
```

Set `VITE_API_BASE_URL` di `frontend/.env` ke URL backend Laragon (contoh: `http://banksampah.test/api/v1` atau `http://localhost:8000/api/v1`).

## Frontend SPA (Vue 3 + Vite)

Struktur minimum:

- `src/api`: sentralisasi axios/client API
- `src/features`: feature-driven module
- `src/stores`: Pinia global state
- `src/router`: Vue Router

Sesuaikan URL API di `frontend/.env` berdasarkan environment yang dipakai (Docker atau Laragon).

## Menjalankan Tanpa Docker & Tanpa Laragon (Local Manual)

### Backend (Laravel API)

```bash
cd backend/bank-sampah-app
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve --host=127.0.0.1 --port=8000
```

Pastikan database lokal sudah aktif dan `.env` disesuaikan:

- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD` sesuai MySQL lokal

### Frontend (Vue)

```bash
cd frontend
npm install
npm run dev
```

Set `VITE_API_BASE_URL` ke `http://127.0.0.1:8000/api/v1`.

## Script Helper (Windows + Bash)

Jika Laragon bermasalah, kamu bisa pakai script Bash ini di Windows melalui **Git Bash** atau **WSL**:

```bash
./scripts/start-local.sh
```

Script ini akan:

- Menjalankan setup backend (composer, key, migrate, seed)
- Menjalankan `php artisan serve` di port 8000
- Menjalankan `npm run dev` di port 5173

Catatan:

- Pastikan PHP 8.3+, Composer, dan Node.js (LTS) sudah terpasang di PATH.
- Log backend ada di `/tmp/banksampah-backend.log` dan frontend di `/tmp/banksampah-frontend.log`.

## Integrasi Xendit

Konfigurasi di `.env` backend:

- `XENDIT_SECRET_KEY`
- `XENDIT_PUBLIC_KEY`
- `XENDIT_CALLBACK_TOKEN`
- `XENDIT_BASE_URL` (optional)

Catatan:

- Autentikasi memakai Basic Auth dengan secret key (username), password kosong.
- Webhook URL diset ke `POST /api/v1/webhooks/xendit/payouts` di dashboard Xendit.

## Unit Test & Cara Menjalankan

Unit test utama mencakup:

- Perhitungan saldo nasabah (setoran, pencairan berhasil, pending)
- Ledger nasabah dan validasi pengajuan pencairan
- Integrasi payout Xendit (mock HTTP)

### Menjalankan Test di Dalam Docker

```bash
docker exec -i banksampah-php sh -c "cd /var/www/html && ./vendor/bin/phpunit"
```

Hanya unit test:

```bash
docker exec -i banksampah-php sh -c "cd /var/www/html && ./vendor/bin/phpunit --testsuite=Unit"
```

Hanya feature test:

```bash
docker exec -i banksampah-php sh -c "cd /var/www/html && ./vendor/bin/phpunit --testsuite=Feature"
```

### Menjalankan Test di Luar Docker

```bash
cd backend/bank-sampah-app
./vendor/bin/phpunit
```

Hanya unit test:

```bash
./vendor/bin/phpunit --testsuite=Unit
```

Hanya feature test:

```bash
./vendor/bin/phpunit --testsuite=Feature
```

## Root index.php untuk Laragon

File root `index.php` sudah disediakan untuk redirect awal ke backend Laravel public agar user non-teknis tidak bingung saat membuka folder project pertama kali.
