<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default View Engine
    |--------------------------------------------------------------------------
    |
    | This option allows you to specify the default view engine that is used
    | by the framework. Laravel supports "blade" as well as "php" view
    | engines out of the box, and you are free to register others as well.
    |
    */

    'default_engine' => 'blade',

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Here you may specify the array of paths that the framework should
    | search when loading views. By default, the "resources/views" path
    | is registered. You are free to add one or more extra paths here.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Path
    |--------------------------------------------------------------------------
    |
    | This option determines the path where the compiled view templates
    | will be cached. You may change this to any location you wish,
    | however, the path should be writable by your web server process.
    |
    */

    'cache' => false,

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines the path where the compiled view templates
    | will be stored. If you would like to customize the path, simply
    | set this option to a custom path. If the path is not writable,
    | the application will not cache views properly.
    |
    */

    'compiled' => base_path('bootstrap/cache/compiled.php'),

    /*
    |--------------------------------------------------------------------------
    | Compiled Extension
    |--------------------------------------------------------------------------
    |
    | This option determines the file extension that should be used
    | for compiled view templates. By default, "php" is used.
    |
    */

    'compiled_extension' => 'php',

    /*
    |--------------------------------------------------------------------------
    | Check Cache Timestamps
    |--------------------------------------------------------------------------
    |
    | This option determines if the framework should check if the view
    | cache has expired. If this is set to true, the framework will
    | re-compile views if the source view file has been modified.
    |
    */

    'check_cache_timestamps' => true,

];