#!/usr/bin/env bash
set -euo pipefail

# Konfigurasi Path
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BACKEND_DIR="$ROOT_DIR/backend/bank-sampah-app"
FRONTEND_DIR="$ROOT_DIR/frontend"

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
echo "==> Processing Backend..."
cd "$BACKEND_DIR"

if [ ! -f .env ]; then
  cp .env.example .env
  echo "Created .env for Backend"
fi

# Logika Hash untuk Composer
if [ -f "composer.lock" ]; then
  CURRENT_COMPOSER_HASH=$(md5sum composer.lock | awk '{ print $1 }')
  
  if [ ! -f .composer.hash ] || [ "$(cat .composer.hash)" != "$CURRENT_COMPOSER_HASH" ] || [ ! -d "vendor" ]; then
    echo "Changes detected or vendor missing. Running composer install..."
    composer install --prefer-dist --no-interaction
    echo "$CURRENT_COMPOSER_HASH" > .composer.hash
  else
    echo "PHP packages are up to date. Skipping install."
  fi
else
  # Jika lock file belum ada (project baru)
  composer install
fi

php artisan key:generate --quiet
php artisan migrate --force
php artisan db:seed --force

# Jalankan backend di background
echo "Starting Laravel server..."
php artisan serve --host=127.0.0.1 --port=8000 >/tmp/banksampah-backend.log 2>&1 &
BACKEND_PID=$!


# --- BAGIAN FRONTEND (VITE/NPM) ---
echo "==> Processing Frontend..."
cd "$FRONTEND_DIR"

if [ ! -f .env ]; then
  cp .env.example .env
  echo "Created .env for Frontend"
fi

# Logika Hash untuk NPM
if [ -f "package-lock.json" ]; then
  CURRENT_NPM_HASH=$(md5sum package-lock.json | awk '{ print $1 }')

  if [ ! -f .npm.hash ] || [ "$(cat .npm.hash)" != "$CURRENT_NPM_HASH" ] || [ ! -d "node_modules" ]; then
    echo "Changes detected or node_modules missing. Running npm install..."
    npm install
    echo "$CURRENT_NPM_HASH" > .npm.hash
  else
    echo "Node packages are up to date. Skipping install."
  fi
else
  # Jika lock file belum ada
  npm install
fi

# Jalankan frontend di background
echo "Starting Vite server..."
npm run dev -- --host 127.0.0.1 --port 5173 >/tmp/banksampah-frontend.log 2>&1 &
FRONTEND_PID=$!

# Output Info
echo "---------------------------------------"
echo "Backend PID: $BACKEND_PID (Port 8000)"
echo "Frontend PID: $FRONTEND_PID (Port 5173)"
echo "Log Backend: /tmp/banksampah-backend.log"
echo "Log Frontend: /tmp/banksampah-frontend.log"
echo "App Ready: http://127.0.0.1:5173"
echo "---------------------------------------"
echo "Press Ctrl+C to stop both servers."

# Trap untuk mematikan kedua proses saat Ctrl+C
trap "kill $BACKEND_PID $FRONTEND_PID; exit" INT TERM

wait $BACKEND_PID $FRONTEND_PID