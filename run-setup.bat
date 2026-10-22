@echo off
setlocal enabledelayedexpansion
title DOOTOR ENTERPRISES - Setup and Operations Helper

:: Determine PHP path (check xampp default or PATH)
set "PHP_CMD=php"
if exist "C:\xampp\php\php.exe" (
    set "PHP_CMD=C:\xampp\php\php.exe"
)

:: Ensure we are running from project root
cd /d "%~dp0"

:: Direct argument execution support (e.g., run-setup.bat migrate)
if not "%~1"=="" (
    if /i "%~1"=="migrate" goto cli_migrate
    if /i "%~1"=="rollback" goto cli_rollback
    if /i "%~1"=="seed" goto cli_seed
    if /i "%~1"=="fresh" goto cli_fresh
    if /i "%~1"=="clear" goto cli_clear
    if /i "%~1"=="optimize" goto cli_optimize
    if /i "%~1"=="serve" goto cli_serve
    if /i "%~1"=="purge" goto cli_purge
    "%PHP_CMD%" artisan %*
    exit /b %ERRORLEVEL%
)

:menu
cls
echo ===================================================================
echo              DOOTOR ENTERPRISES - RUN-SETUP HELPER
echo ===================================================================
echo  PHP Path: %PHP_CMD%
echo  Location: %CD%
echo ===================================================================
echo.
echo  [1] Run Database Migrations (php artisan migrate)
echo  [2] Rollback Last Migration (php artisan migrate:rollback)
echo  [3] Run Database Seeders (php artisan db:seed)
echo  [4] Migrate and Seed Fresh [WIPES DB] (php artisan migrate:fresh --seed)
echo  [5] Clear All Caches (config, route, view, app cache)
echo  [6] Optimize and Cache (config, route, view cache)
echo  [7] Create Storage Symlink (php artisan storage:link)
echo  [8] Run Expirations Purge Manually (applications:check-expirations)
echo  [9] Start Local Laravel Dev Server (php artisan serve)
echo  [0] Open Interactive Artisan Terminal / Custom Command
echo  [Q] Exit
echo.
echo ===================================================================
set /p opt="Select an option (0-9, Q): "

if /i "%opt%"=="1" goto migrate
if /i "%opt%"=="2" goto rollback
if /i "%opt%"=="3" goto seed
if /i "%opt%"=="4" goto fresh
if /i "%opt%"=="5" goto clear_cache
if /i "%opt%"=="6" goto optimize
if /i "%opt%"=="7" goto storage_link
if /i "%opt%"=="8" goto purge_expirations
if /i "%opt%"=="9" goto serve
if /i "%opt%"=="0" goto custom_cmd
if /i "%opt%"=="q" goto end
if /i "%opt%"=="exit" goto end

echo Invalid selection. Please try again.
pause
goto menu

:migrate
echo.
echo [*] Running Database Migrations...
"%PHP_CMD%" artisan migrate --force
echo.
echo [Done]
pause
goto menu

:rollback
echo.
echo [*] Rolling back last migration step...
"%PHP_CMD%" artisan migrate:rollback
echo.
echo [Done]
pause
goto menu

:seed
echo.
echo [*] Running Database Seeders...
"%PHP_CMD%" artisan db:seed --force
echo.
echo [Done]
pause
goto menu

:fresh
echo.
echo [!] WARNING: This will drop all tables and re-run all migrations and seeders!
set /p confirm="Are you sure you want to proceed? (y/N): "
if /i not "%confirm%"=="y" (
    echo Cancelled.
    pause
    goto menu
)
"%PHP_CMD%" artisan migrate:fresh --seed --force
echo.
echo [Done]
pause
goto menu

:clear_cache
echo.
echo [*] Clearing application caches...
"%PHP_CMD%" artisan config:clear
"%PHP_CMD%" artisan cache:clear
"%PHP_CMD%" artisan route:clear
"%PHP_CMD%" artisan view:clear
"%PHP_CMD%" artisan optimize:clear
echo.
echo [All caches cleared successfully!]
pause
goto menu

:optimize
echo.
echo [*] Optimizing and caching configuration, routes, and views...
"%PHP_CMD%" artisan config:cache
"%PHP_CMD%" artisan route:cache
"%PHP_CMD%" artisan view:cache
echo.
echo [Optimization complete!]
pause
goto menu

:storage_link
echo.
echo [*] Linking storage/app/public to public/storage...
"%PHP_CMD%" artisan storage:link
echo.
echo [Done]
pause
goto menu

:purge_expirations
echo.
echo [*] Running Application Expirations and Lifecycle Purge...
"%PHP_CMD%" artisan applications:check-expirations
echo.
echo [Done]
pause
goto menu

:serve
echo.
echo [*] Starting Laravel development server at http://127.0.0.1:8000 ...
echo Press Ctrl+C to stop the server and return.
echo.
"%PHP_CMD%" artisan serve
pause
goto menu

:custom_cmd
echo.
echo Type your artisan arguments (e.g. "migrate:status", "make:model Sample", or "tinker"):
set /p custom_args="php artisan "
if not "%custom_args%"=="" (
    "%PHP_CMD%" artisan %custom_args%
)
echo.
pause
goto menu

:: CLI direct execution jumps
:cli_migrate
"%PHP_CMD%" artisan migrate --force
exit /b %ERRORLEVEL%

:cli_rollback
"%PHP_CMD%" artisan migrate:rollback
exit /b %ERRORLEVEL%

:cli_seed
"%PHP_CMD%" artisan db:seed --force
exit /b %ERRORLEVEL%

:cli_fresh
"%PHP_CMD%" artisan migrate:fresh --seed --force
exit /b %ERRORLEVEL%

:cli_clear
"%PHP_CMD%" artisan optimize:clear
exit /b %ERRORLEVEL%

:cli_optimize
"%PHP_CMD%" artisan config:cache
"%PHP_CMD%" artisan route:cache
"%PHP_CMD%" artisan view:cache
exit /b %ERRORLEVEL%

:cli_serve
"%PHP_CMD%" artisan serve
exit /b %ERRORLEVEL%

:cli_purge
"%PHP_CMD%" artisan applications:check-expirations
exit /b %ERRORLEVEL%

:end
echo Goodbye!
exit /b 0
