#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BACKEND_DIR="$ROOT_DIR/backend/bank-sampah-app"
FRONTEND_DIR="$ROOT_DIR/frontend"

if ! command -v php >/dev/null 2>&1; then
  echo "PHP not found. Install PHP 8.3+ and try again."
  exit 1
fi

if ! command -v composer >/dev/null 2>&1; then
  echo "Composer not found. Install Composer and try again."
  exit 1
fi

if ! command -v npm >/dev/null 2>&1; then
  echo "npm not found. Install Node.js (LTS) and try again."
  exit 1
fi

echo "==> Starting backend (Laravel)"
cd "$BACKEND_DIR"
if [ ! -f .env ]; then
  cp .env.example .env
fi
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve --host=127.0.0.1 --port=8000 >/tmp/banksampah-backend.log 2>&1 &
BACKEND_PID=$!

cd "$FRONTEND_DIR"
if [ ! -f .env ]; then
  cp .env.example .env
fi
npm install
npm run dev -- --host 127.0.0.1 --port 5173 >/tmp/banksampah-frontend.log 2>&1 &
FRONTEND_PID=$!

echo "Backend PID: $BACKEND_PID"
echo "Frontend PID: $FRONTEND_PID"
echo "Backend log: /tmp/banksampah-backend.log"
echo "Frontend log: /tmp/banksampah-frontend.log"
echo "Open: http://127.0.0.1:5173"

wait $BACKEND_PID $FRONTEND_PID
