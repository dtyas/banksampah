#!/usr/bin/env bash
set -euo pipefail

# 1. KONFIGURASI PATH
# Mengambil path absolut dari folder tempat script ini berada
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$ROOT_DIR/backend/bank-sampah-app"
FRONTEND_DIR="$ROOT_DIR/frontend"
LOG_DIR="$ROOT_DIR/logs"

# Buat folder log jika belum ada
mkdir -p "$LOG_DIR"

# Optimasi khusus Windows agar PHP CLI lebih stabil
export PHP_FCGI_CHILDREN=0

# Fungsi untuk mengecek apakah aplikasi pendukung sudah terinstall
check_command() {
  if ! command -v "$1" >/dev/null 2>&1; then
    echo "❌ Error: $1 tidak ditemukan. Pastikan $1 masuk ke Environment Variables Windows."
    exit 1
  fi
}

echo "🔍 Checking dependencies..."
check_command php
check_command composer
check_command npm

# Fungsi Hashing yang cepat untuk Windows
get_hash() {
    if command -v git >/dev/null 2>&1; then
        git hash-object "$1"
    else
        md5sum "$1" | awk '{ print $1 }'
    fi
}

# --- BAGIAN 1: BACKEND (LARAVEL) ---
echo "==> [1/3] Processing Backend..."
if [ ! -d "$BACKEND_DIR" ]; then
    echo "❌ Error: Folder $BACKEND_DIR tidak ditemukan!"
    exit 1
fi

cd "$BACKEND_DIR"

# Setup .env Laravel
[ ! -f .env ] && cp .env.example .env && echo "✅ Created .env for Backend"

# Cek apakah perlu menjalankan composer install
if [ -f "composer.lock" ]; then
  CURRENT_COMPOSER_HASH=$(get_hash composer.lock)
  if [ ! -f .composer.hash ] || [ "$(cat .composer.hash)" != "$CURRENT_COMPOSER_HASH" ] || [ ! -d "vendor" ]; then
    echo "📦 Changes detected. Running composer install..."
    composer install --prefer-dist --no-interaction --no-progress --optimize-autoloader
    echo "$CURRENT_COMPOSER_HASH" > .composer.hash
  else
    echo "✅ PHP packages are up to date."
  fi
else
  echo "📦 No lock file found. Running composer install..."
  composer install --prefer-dist
fi

php artisan key:generate --quiet
php artisan migrate --force

# --- BAGIAN 2: FRONTEND (VITE) ---
echo "==> [2/3] Processing Frontend..."
cd "$FRONTEND_DIR"

# Setup .env Frontend
[ ! -f .env ] && cp .env.example .env && echo "✅ Created .env for Frontend"

# Cek apakah perlu menjalankan npm install
if [ -f "package-lock.json" ]; then
  CURRENT_NPM_HASH=$(get_hash package-lock.json)
  if [ ! -f .npm.hash ] || [ "$(cat .npm.hash)" != "$CURRENT_NPM_HASH" ] || [ ! -d "node_modules" ]; then
    echo "📦 Changes detected. Running npm install..."
    npm install --no-audit --no-fund
    echo "$CURRENT_NPM_HASH" > .npm.hash
  else
    echo "✅ Node packages are up to date."
  fi
else
  echo "📦 No lock file found. Running npm install..."
  npm install
fi

# --- BAGIAN 3: RUNNING SERVICES ---
echo "==> [3/3] Starting All Services..."

# 1. Laravel Server
cd "$BACKEND_DIR"
php artisan serve --host=127.0.0.1 --port=8000 > "$LOG_DIR/backend.log" 2>&1 &
BACKEND_PID=$!

# 2. Queue Worker (Otomatis restart jika file berubah)
php artisan queue:work --watch > "$LOG_DIR/queue.log" 2>&1 &
QUEUE_PID=$!

# 3. Vite Server (Frontend)
cd "$FRONTEND_DIR"
npm run dev -- --host 127.0.0.1 --port 5173 > "$LOG_DIR/frontend.log" 2>&1 &
FRONTEND_PID=$!

# --- OUTPUT INFO ---
echo "------------------------------------------------"
echo "🚀 SEMUA SERVIS SUDAH BERJALAN"
echo "------------------------------------------------"
echo "🌐 App URL:    http://127.0.0.1:5173"
echo "🖥️  Backend:    PID $BACKEND_PID (Port 8000)"
echo "⚙️  Queue:      PID $QUEUE_PID (Running)"
echo "🎨 Frontend:   PID $FRONTEND_PID (Port 5173)"
echo "------------------------------------------------"
echo "📄 Logs:       $LOG_DIR"
echo "⌨️  Tekan [Ctrl+C] untuk menghentikan semua servis."

# --- CLEANUP (PENTING UNTUK WINDOWS) ---
cleanup() {
    echo -e "\n🛑 Stopping all services..."
    
    # Mencoba mematikan secara halus (Unix-style)
    kill $BACKEND_PID $QUEUE_PID $FRONTEND_PID 2>/dev/null || true
    
    # Cadangan khusus Windows: Memastikan tidak ada proses yang nyangkut di port
    # Jika sering error "Port already in use", aktifkan 2 baris di bawah ini:
    # taskkill //F //IM php.exe //T >/dev/null 2>&1 || true
    # taskkill //F //IM node.exe //T >/dev/null 2>&1 || true
    
    echo "✅ Done. Bye!"
    exit
}

# Tangkap sinyal interrupt (Ctrl+C)
trap cleanup INT TERM

# Menjaga script tetap hidup untuk memonitor background processes
wait