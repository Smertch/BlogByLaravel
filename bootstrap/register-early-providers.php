<?php

use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\View\ViewServiceProvider;

return function ($app) {
    $app->register(FilesystemServiceProvider::class);
    $app->register(ViewServiceProvider::class);
};
