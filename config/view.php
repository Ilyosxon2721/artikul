<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. We use a plain path() call (not realpath) so that the
    | directory does not need to exist beforehand — Laravel will create it
    | on the fly during the first compile. This avoids the
    |   "Please provide a valid cache path"
    | error on first deploy when storage/framework/views/ has not been
    | provisioned yet.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        storage_path('framework/views'),
    ),

];
