@echo off
title Universitas Angolia - Server Portal Kampus
color 0B
cls

echo ========================================================
echo        PORTAL AKADEMIK UNIVERSITAS ANGOLIA
echo   Fostering and Empowering - Modern University Portal
echo ========================================================
echo.

:: 1. Deteksi executable PHP
set "PHP_CMD="

where php >nul 2>nul
if %errorlevel% equ 0 (
    set "PHP_CMD=php"
    goto START_SERVER
)

if exist "C:\xampp\php\php.exe" (
    set "PHP_CMD=C:\xampp\php\php.exe"
    goto START_SERVER
)

if exist "D:\xampp\php\php.exe" (
    set "PHP_CMD=D:\xampp\php\php.exe"
    goto START_SERVER
)

if exist "C:\laragon\bin\php\php-8.2*\php.exe" (
    for /f "delims=" %%I in ('dir /b /s "C:\laragon\bin\php\php.exe" 2^>nul') do (
        set "PHP_CMD=%%I"
        goto START_SERVER
    )
)

:NOT_FOUND
echo [ERROR] PHP tidak terdeteksi di sistem PATH atau direktori C:\xampp\php!
echo Pastikan XAMPP atau PHP sudah terinstal.
echo.
pause
exit /b

:START_SERVER
echo [INFO] Menggunakan runtime: %PHP_CMD%
echo [INFO] Menyiapkan server lokal di http://localhost:8000 ...
echo [INFO] Browser akan terbuka secara otomatis!
echo.
echo ========================================================
echo Akun Demo Latihan:
echo   - Mahasiswa : mahasiswa / mhs123
echo   - Dosen     : dosen / dosen123
echo   - Admin     : admin / admin123
echo ========================================================
echo.
echo Tekan CTRL + C di jendela ini untuk mematikan server.
echo.

:: Buka browser otomatis setelah 1 detik
start "" cmd /c "timeout /t 1 /nobreak >nul & start http://localhost:8000"

:: Jalankan Built-in Web Server PHP
cd /d "%~dp0"
"%PHP_CMD%" -S localhost:8000
pause
