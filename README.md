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
```bash
Description:
  Make API Resources

Usage:
  make:api [options] [--] <name>

Arguments:
  name                       The name of the Model

Options:
  -c, --x-controller         Create an API without new controller for the model
  -f, --x-factory            Create an API without new factory for the model
  -m, --x-migration          Create an API without new migration file for the model
  -M, --x-model              Create an API without new migration file for the model
  -p, --x-policy             Create an API without new policy file for the model
  -r, --x-request            Create an API without new request file for the model
  -s, --x-seeder             Create an API without new seeder file for the model
  -t, --x-test               Create an API without new test file for the model
  -u, --x-test.unauthorized  Create an API without new unauthorized test file for the model
  -P, --pretend              Dump the code that would be created
  -h, --help                 Display this help message
  -q, --quiet                Do not output any message
  -V, --version              Display this application version
      --ansi                 Force ANSI output
      --no-ansi              Disable ANSI output
  -n, --no-interaction       Do not ask any interactive question
      --env[=ENV]            The environment the command should run under
  -v|vv|vvv, --verbose       Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```
* Selanjutnya, buka file App/Console/Kernel.php, pada bagian  protected $commands tambahkan:
```php
\Febrianrz\Makeapi\ApiMakeCommand::class
```
