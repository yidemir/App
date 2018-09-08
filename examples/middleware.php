<?php

use Demir\App;

require __DIR__ . '/../vendor/autoload.php';

// simple middleware
function check_admin()
{
  if ('admin' !== 'ad_min') {
    App::redirect('auth.login');
    exit();
  }
}

// simple middleware class
class CheckAdmin
{
  public function __invoke()
  {
  if ('admin' !== 'ad_min') {
    App::redirect('auth.login');
    exit();
  }
  }
}

App::get('/middleware.php', function(){
  // ...
}, 'homepage', ['check_admin']/* or [new CheckAdmin()] */);

App::get('/middleware.php/auth/login', function(){
  // ...
  echo 'Please login';
}, 'auth.login');

App::run();