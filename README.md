# micromiddleware
Make API

## Installasi
```bash
composer require febrianrz/micromidlleware
```

## Penggunaan
* Pada file app/Http/Kernel.php pada bagian $routeMiddleware tambahkan
```bash
'auth.micro'    => \Febrianrz\Micromidlleware\MicroAuthenticate::class,
'auth.micro-app'=> \Febrianrz\Micromidlleware\MicroAppAuthenticate::class,
```
* Pada bagian api.php, gunakan middleware 
```bash
Route::middleware('auth.micro')
```
* Pada setiap request, data user dapat digunakan dengan cara
```bash
$request->user
```
* Lakukan publish configurasi dengan perintah
```bash
php artisan vendor:publish
```