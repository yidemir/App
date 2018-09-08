<?php

use Demir\App;

require __DIR__ . '/../vendor/autoload.php';

$viewsPath = __DIR__ . '/views';
App::container()->set('views.path', $viewsPath);

App::get('/view.php', function(){
  $data = ['foo' => 'bar'];
  
  App::render('home');
  // or
  App::view('home')->show();
  // or
  echo App::view('home');
  // or
  App::render('home', $data);
  // or
  App::view('home')->with('foo', 'bar')->show();
  // or
  echo App::view('home')->with($data);
});

App::run();