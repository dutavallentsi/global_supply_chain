@echo off
REM ============================================================
REM Laravel Scheduler Runner untuk SCM Risk Monitor
REM Dipanggil oleh Windows Task Scheduler setiap 1 menit.
REM Meniru cron job "* * * * * php artisan schedule:run" di Linux.
REM ============================================================

cd /d C:\xampp\htdocs\project-akhir
C:\xampp\php\php.exe artisan schedule:run >> storage\logs\scheduler.log 2>&1