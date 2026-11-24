@echo off
setlocal
REM Build-EXE.cmd - Build aplikasi desktop Gereja Cleaner menjadi file .exe siap jalan (Windows x64)
cd /d "%~dp0"

echo ==================================================
echo  Build Gereja Cleaner Desktop (.exe Windows x64)
echo ==================================================

where node >nul 2>&1
if errorlevel 1 (
  echo [ERROR] Node.js tidak ditemukan di PATH. Install Node.js terlebih dahulu.
  pause
  exit /b 1
)

if not exist "node_modules\mysql2" (
  echo [1/3] Install dependencies...
  call npm install
  if errorlevel 1 (
    echo [ERROR] Gagal menjalankan npm install.
    pause
    exit /b 1
  )
) else (
  echo [1/3] Dependencies sudah terpasang.
)

echo [2/3] Memaketkan aplikasi menjadi .exe...
call npx --yes @electron/packager . "Gereja-Cleaner" --platform=win32 --arch=x64 --out=dist --overwrite --ignore="^/dist" --ignore="^/Build-EXE\.cmd$" --ignore="^/Jalankan\.bat$"
if errorlevel 1 (
  echo [ERROR] Gagal membuild file .exe. Periksa koneksi internet atau log di atas.
  pause
  exit /b 1
)

echo.
echo [3/3] Build selesai!
echo File siap jalan: "%~dp0dist\Gereja-Cleaner-win32-x64\Gereja-Cleaner.exe"
echo.
echo Tip: Jika ingin membaca config database otomatis, salin file .env ke folder yang sama dengan Gereja-Cleaner.exe.
echo.
explorer "%~dp0dist\Gereja-Cleaner-win32-x64"
pause
