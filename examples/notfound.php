<?php

use Demir\App;

require __DIR__ . '/../vendor/autoload.php';

App::notFound(function(){
  echo '404 error, page not found';
});

App::run();