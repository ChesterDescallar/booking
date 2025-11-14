@echo off
echo Setting up Booking Application Demo...
echo.

REM Create SQLite database
echo Creating SQLite database...
type nul > database\database.sqlite

REM Run migrations
echo Running migrations...
php artisan migrate:fresh

REM Seed demo data
echo Seeding demo data...
php artisan db:seed --class=DemoDataSeeder

echo.
echo Done! Demo setup complete!
echo.
echo Demo Users (password: 'password'):
echo   - john@example.com (John Smith)
echo   - sarah@example.com (Sarah Johnson)
echo   - michael@example.com (Michael Brown)
echo   - emily@example.com (Emily Davis)
echo   - admin@example.com (Admin User)
echo.
echo Demo Clients:
echo   - Acme Corporation
echo   - Tech Solutions Inc
echo   - Global Enterprises
echo   - Innovate Labs
echo   - Creative Studios
echo.
echo 7 demo bookings have been created for the next 8 days.
echo.
echo To start the application:
echo   php artisan serve
echo.
pause
