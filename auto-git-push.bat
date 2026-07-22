@echo off
setlocal EnableDelayedExpansion

cd /d "%~dp0"

echo ========================================
echo   AUTO GIT ADD / COMMIT / PUSH
echo ========================================
echo.

where git >nul 2>nul
if errorlevel 1 (
    echo Git tidak ditemukan di PATH.
    pause
    exit /b 1
)

for /f "delims=" %%i in ('git branch --show-current 2^>nul') do set "CURRENT_BRANCH=%%i"
if not defined CURRENT_BRANCH (
    echo Gagal membaca branch aktif.
    pause
    exit /b 1
)

echo Branch aktif: %CURRENT_BRANCH%
echo.
echo 5 commit message sebelumnya:
git log --pretty=format:"- %%s" -n 5
if errorlevel 1 (
    echo Gagal membaca riwayat commit.
    pause
    exit /b 1
)

echo.
echo.
set /p "COMMIT_MESSAGE=Masukkan commit message: "

if not defined COMMIT_MESSAGE (
    echo Commit message tidak boleh kosong.
    pause
    exit /b 1
)

git status --porcelain >nul 2>nul
if errorlevel 1 (
    echo Repository git tidak valid atau status gagal dibaca.
    pause
    exit /b 1
)

for /f %%i in ('git status --porcelain ^| find /c /v ""') do set "CHANGED_COUNT=%%i"
if "%CHANGED_COUNT%"=="0" (
    echo Tidak ada perubahan untuk di-commit.
    pause
    exit /b 0
)

echo.
echo Menambahkan semua perubahan...
git add .
if errorlevel 1 (
    echo Gagal menjalankan git add.
    pause
    exit /b 1
)

echo.
echo Membuat commit...
git commit -m "%COMMIT_MESSAGE%"
if errorlevel 1 (
    echo Gagal membuat commit.
    pause
    exit /b 1
)

echo.
echo Push ke origin/%CURRENT_BRANCH%...
git push origin %CURRENT_BRANCH%
if errorlevel 1 (
    echo Gagal melakukan push.
    pause
    exit /b 1
)

echo.
echo Selesai.
pause
exit /b 0
