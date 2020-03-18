# micromiddleware
Make API

## Dependency
* Yajra datatable
* webpatser/laravel-uuid

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
* Selanjutnya, edit file App/Controller.php menjadi:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use DispatchesJobs;
    use ValidatesRequests;
    use AuthorizesRequests {
        resourceAbilityMap as protected resourceAbilityMapTrait;
    }

    protected $model = null;
    protected $relationships = [];

    public function __construct()
    {
        if ($this->model) {
            $this->authorizeResource($this->model);
        }
    }

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        // Map the "index" ability to the "list" function in our policies
        return array_merge($this->resourceAbilityMapTrait(), ['index' => 'list']);
    }

    public function loadRelationships($model)
    {
        // dd($this->relationships);
        $relationships = [];
        
        if (request()->has('load')) {
            $relationships = request()->load;

            if (!is_array($relationships)) {
                $relationships = [$relationships];
            }
        }

        $relationships = array_unique(array_merge($relationships, $this->relationships));
        // dd($relationships);
        // dd(instanceof $model);
        if ($model instanceof Model) {
            // dd("model");
            $model->load($relationships);
        } else{
            $model->with($relationships);
        }
    }
}
```
