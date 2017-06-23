#!/bin/bash

php artisan migrate:reset
php artisan migrate --package="lucadegasperi/oauth2-server-laravel"
php artisan migrate --path=src/oPheme/Database/Migrations
php artisan db:seed --class=DevelopmentDatabaseSeeder