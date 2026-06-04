<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        storage_path('framework/views')
    ),

    /*
    |--------------------------------------------------------------------------
    | View cache (compiled Blade)
    |--------------------------------------------------------------------------
    |
    | When false, compiled Blade is treated as stale every request (handy for local
    | dev). Use true in production.
    |
    */

    'cache' => env('VIEW_CACHE_COMPILED', true),

    'check_cache_timestamps' => env('VIEW_CHECK_CACHE_TIMESTAMPS', true),

    'relative_hash' => env('VIEW_RELATIVE_HASH', false),

    'compiled_extension' => env('VIEW_COMPILED_EXTENSION', 'php'),

];
