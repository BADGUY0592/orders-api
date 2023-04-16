# orders-api

## Installation
```sh
composer install
php artisan key:generate
composer dump-autoload
```

## Setup your Database
- Set your database credentials in .env file.
- change FILESYSTEM_DRIVER to public in .env file
- run ```php artisan config:cache ```
- run ```php artisan migrate ```

Run ```php artisan serve ```
