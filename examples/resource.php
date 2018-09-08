<?php

use Demir\App;

require __DIR__ . '/../vendor/autoload.php';

App::resource('/posts', Controllers\PostController::class);
App::resource('/categories', Controllers\PostController::class, ['check_admin']);

App::run();