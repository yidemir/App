<?php

use Demir\App;

require __DIR__ . '/../vendor/autoload.php';

App::get('/json.php', function(){
  $posts = [
    ['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4], ['id' => 5]
  ];

  return App::json($posts);
});

App::run();