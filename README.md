# micromiddleware
Make API

## Installasi
```bash
composer require febrianrz/makeapi
```

## Penggunaan
* Via CLI
```bash
php artisan make:api NamaModel
```
* Selanjutnya, buka file App/Console/Kernel.php, pada bagian  protected $commands tambahkan:
```php
\Febrianrz\Makeapi\ApiMakeCommand::class
```
