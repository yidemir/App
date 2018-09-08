<?php

use Demir\App;

require __DIR__ . '/../vendor/autoload.php';

App::get('/url.php', function(){
  $id = 5;
  echo App::url('show.post', $id);
});

App::get('/url.php/posts/show/:id', function($id){
  echo $id;
}, 'show.post');

App::run();