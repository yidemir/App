<?php

use Demir\App;

require __DIR__ . '/../vendor/autoload.php';

App::error(function($e){
  echo $e->getMessage();
});

App::get('/error.php', function(){
  throw new \Exception('Error');
});

App::run();