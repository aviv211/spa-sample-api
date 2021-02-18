# Laravel API for Angular Project by Vince Christian Dilig

 API for Example Angular Project by Vince Christian Dilig (https://github.com/shizuwons/spa-sample).

## Prerequisites

- PHP
- Composer
- XAMPP/MAMP or any PHP database hosting service
- Mailer (Amazon SES or Mailtrap)

## Installation

To install, run `composer install` to install all dependencies needed for this project.

For mailer configuration, you can use AWS SES if available. For alternatives, you can use Mailtrap.

For mailtrap:
1. Go to the mailtrap website. (https://mailtrap.io)
2. Create an account.
3. Go to the dashboard and click on the small green icon.
4. Copy the username and password from the SMTP settings tab.
5. In this project's `.env` file, add the mailtrap details.

## Setting up databases

After creating a database, run `php artisan migrate` to fill the database with tables needed for the API to work properly. After migrating, run `php artisan passport:install` to generate encryptions keys needed for authentication/login.

## Filling database with sample user data

This is optional but you can run `php artisan db:seed` to create a sample user.

## Development Server

Run `php artisan serve` for the backend development server.