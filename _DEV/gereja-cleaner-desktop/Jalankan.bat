@echo off
REM Jalankan.bat - Launcher aplikasi desktop Gereja Cleaner (klik 2x)
cd /d "%~dp0"
echo ============================================
echo  Gereja Cleaner - Data Wilayah Kosong
echo ============================================

where node >nul 2>&1
if errorlevel 1 (
  echo GAGAL: Node.js tidak ditemukan di PATH. Install Node.js terlebih dahulu.
  pause
  exit /b 1
)

if not exist "node_modules\electron" (
  echo [1/2] Install dependencies pertama kali...
  call npm install
  if errorlevel 1 (
    echo GAGAL install. Cek koneksi internet lalu jalankan lagi.
    pause
    exit /b 1
  )
)

echo [2/2] Membuka aplikasi desktop...
if exist "node_modules\.bin\electron.cmd" (
  call "node_modules\.bin\electron.cmd" .
) else (
  call npx electron .
)
if errorlevel 1 pause
