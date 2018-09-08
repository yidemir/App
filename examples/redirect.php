<?php

use Demir\App;

require __DIR__ . '/../vendor/autoload.php';

App::get('/redirect.php', function(){
  App::redirect('hello.world');

  // or redirect to google
  // \header('Location: https://google.com');
});

App::get('/redirect.php/hello/world', function(){
  echo 'Hello world!';
}, 'hello.world');

App::run();