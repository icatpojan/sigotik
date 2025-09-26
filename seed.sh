#!/bin/bash

# bersihin cache biar aman
php artisan optimize:clear
php artisan migrate:refresh
composer dump-autoload

# jalanin seeder utama
php artisan db:seed --class="Database\\Seeders\\DatabaseSeeder"
