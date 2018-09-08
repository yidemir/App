<?php

use Demir\App;

require __DIR__ . '/../vendor/autoload.php';

// example closure
function hello_world() {
  echo 'Hello world!';
}

// example class
class HelloWorld
{
  public function __invoke()
  {
    echo 'Hello world from __invoke()!';
  }

  public function index()
  {
    echo 'Hello world from index()!';
  }

  public static function create()
  {
    echo 'Hello world from create()!';
  }
}

App::get('/routes.php', function(){
  echo 'Hello world!';
}, 'route.name');

App::post('/routes.php', 'hello_world');
App::put('/routes.php', new HelloWorld());
App::patch('/routes.php', [new HelloWorld(), 'index']);
App::delete('/routes.php', 'HelloWorld:index');
App::any('/routes.php', 'HelloWorld::create');

App::run();