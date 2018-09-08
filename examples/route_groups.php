<?php

use Demir\App;

require __DIR__ . '/../vendor/autoload.php';

App::get('/route_groups.php', function(){
  echo 'Welcome home!';
});

App::group('/route_groups.php/admin', function(){

  App::get('/', function(){
    echo 'Welcome admin dashboard!';
  }, 'dashboard'); // name is admin.dashboard

  App::resource('/posts', App\Controllers\Admin\PostController::class);

}, 'admin.');

App::run();