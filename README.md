# spa-sample-api
 API for Example Angular Project

## Installation
To install, run `composer install` to install all dependencies needed for this project.

## Setting up databases
After creating a database, run `php artisan migrate` to fill the database with tables needed for the API to work properly. After migrating, run `php artisan passport:install` to generate encryptions keys needed for authentication/login.

## Filling database with sample user data
This is optional but you can run `php artisan db:seed` to create a sample user.