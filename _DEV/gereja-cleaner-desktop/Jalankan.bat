@echo off
REM Jalankan.bat - Launcher aplikasi desktop Gereja Cleaner (klik 2x)
cd /d "%~dp0"
echo ============================================
echo  Gereja Cleaner - Data Wilayah Kosong
echo ============================================
if not exist "node_modules" (
  echo [1/2] Install dependencies pertama kali...
  call "C:\Program Files\nodejs\npm.cmd" install
  if errorlevel 1 (
    echo GAGAL install. Cek koneksi internet lalu jalankan lagi.
    pause
    exit /b 1
  )
)
echo [2/2] Membuka aplikasi desktop...
call "C:\Program Files\nodejs\npx.cmd" electron .
if errorlevel 1 pause
