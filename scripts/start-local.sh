#!/usr/bin/env bash
set -euo pipefail

# 1. Konfigurasi Path & Environment
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$ROOT_DIR/backend/bank-sampah-app"
FRONTEND_DIR="$ROOT_DIR/frontend"
LOG_DIR="$ROOT_DIR/logs"

# Optimasi untuk Windows (Git Bash)
export PHP_FCGI_CHILDREN=0
mkdir -p "$LOG_DIR"

# Fungsi validasi command
check_command() {
  if ! command -v "$1" >/dev/null 2>&1; then
    echo "Error: $1 tidak ditemukan. Pastikan sudah terinstall."
    exit 1
  fi
}

check_command php
check_command composer
check_command npm

# --- BAGIAN BACKEND (LARAVEL) ---
echo "==> [1/3] Processing Backend..."
cd "$BACKEND_DIR"

[ ! -f .env ] && cp .env.example .env && echo "Created .env for Backend"

# Logika Hash Composer
get_hash() {
    if command -v git >/dev/null 2>&1; then
        git hash-object "$1"
    else
        md5sum "$1" | awk '{ print $1 }'
    fi
}

if [ -f "composer.lock" ]; then
  CURRENT_COMPOSER_HASH=$(get_hash composer.lock)
  if [ ! -f .composer.hash ] || [ "$(cat .composer.hash)" != "$CURRENT_COMPOSER_HASH" ] || [ ! -d "vendor" ]; then
    echo "Changes detected. Running composer install..."
    composer install --prefer-dist --no-interaction --no-progress --optimize-autoloader
    echo "$CURRENT_COMPOSER_HASH" > .composer.hash
  else
    echo "PHP packages are up to date."
  fi
else
  composer install --prefer-dist
fi

php artisan key:generate --quiet
php artisan migrate --force

# --- JALANKAN SERVIS BACKEND ---
echo "Starting Laravel Server & Queue Worker..."

# 1. Jalankan PHP Artisan Serve
php artisan serve --host=127.0.0.1 --port=8000 > "$LOG_DIR/backend.log" 2>&1 &
BACKEND_PID=$!

# 2. Jalankan Queue Worker (Otomatis restart jika kodingan berubah dengan --watch)
# Note: Jika Laravel versi < 10, gunakan 'php artisan queue:listen'
php artisan queue:work --watch > "$LOG_DIR/queue.log" 2>&1 &
QUEUE_PID=$!


# --- BAGIAN FRONTEND (VITE/NPM) ---
echo "==> [2/3] Processing Frontend..."
cd "$FRONTEND_DIR"

[ ! -f .env ] && cp .env.example .env && echo "Created .env for Frontend"

if [ -f "package-lock.json" ]; then
  CURRENT_NPM_HASH=$(get_hash package-lock.json)
  if [ ! -f .npm.hash ] || [ "$(cat .npm.hash)" != "$CURRENT_NPM_HASH" ] || [ ! -d "node_modules" ]; then
    echo "Running npm install..."
    npm install --no-audit --no-fund
    echo "$CURRENT_NPM_HASH" > .npm.hash
  else
    echo "Node packages are up to date."
  fi
else
  npm install
fi

# 3. Jalankan Vite
echo "Starting Vite server..."
npm run dev -- --host 127.0.0.1 --port 5173 > "$LOG_DIR/frontend.log" 2>&1 &
FRONTEND_PID=$!


# --- BAGIAN FINISHING ---
echo "------------------------------------------------"
echo "✅ SEMUA SERVIS BERJALAN"
echo "------------------------------------------------"
echo "🌐 App URL:    http://127.0.0.1:5173"
echo "🖥️  Backend:    PID $BACKEND_PID (Port 8000)"
echo "⚙️  Queue:      PID $QUEUE_PID (Running)"
echo "🎨 Frontend:   PID $FRONTEND_PID (Port 5173)"
echo "------------------------------------------------"
echo "📄 Logs:       $LOG_DIR"
echo "⌨️  Tekan [Ctrl+C] untuk menghentikan semua servis."

# Fungsi Cleanup untuk mematikan semua PID saat script ditutup
cleanup() {
    echo -e "\nStopping all services (PIDs: $BACKEND_PID $QUEUE_PID $FRONTEND_PID)..."
    kill $BACKEND_PID $QUEUE_PID $FRONTEND_PID 2>/dev/null
    exit
}

trap cleanup INT TERM

# Menjaga script tetap hidup untuk memantau proses background
wait