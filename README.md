# Bank Sampah - Decoupled Architecture

Project ini sudah dipisah menjadi:

- Backend: Laravel API v1 di `backend/bank-sampah-app`
- Frontend: Vue 3 + Vite SPA di `frontend`

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

## Endpoint Auth

- `POST /api/v1/auth/login`
- `GET /api/v1/auth/me` (auth:sanctum)
- `POST /api/v1/auth/logout` (auth:sanctum)

## Setup Docker (Helper Developer)

Jalankan dari root project:

```bash
docker compose up -d --build
```

Service:

- `nginx` (Nginx + PHP-FPM 8.3): `http://localhost:8000`
- `php` (PHP-FPM service)
- `mysql` (MySQL 8.4): host `127.0.0.1`, port `3307`
- `node` (Vite dev server): `http://localhost:5173`

Setelah container jalan, inisialisasi Laravel:

```bash
cd backend/bank-sampah-app
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Setup Laragon (Klien Windows)

Klien cukup jalankan project tanpa Docker.

Perhatikan `backend/bank-sampah-app/.env`:

- Laragon: `DB_HOST=127.0.0.1`
- Docker: `DB_HOST=mysql`

`backend/bank-sampah-app/.env.example` sekarang hanya placeholder kosong, jadi pastikan isi sendiri sesuai environment.

## Frontend SPA (Vue 3 + Vite)

Struktur minimum:

- `src/api`: sentralisasi axios/client API
- `src/features`: feature-driven module
- `src/stores`: Pinia global state
- `src/router`: Vue Router

Jalankan frontend lokal:

```bash
cd frontend
npm install
npm run dev
```

Sesuaikan URL API di `frontend/.env` berdasarkan environment.

## Root index.php untuk Laragon

File root `index.php` sudah disediakan untuk redirect awal ke backend Laravel public agar user non-teknis tidak bingung saat membuka folder project pertama kali.
