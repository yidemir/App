<?php

use Demir\App;

require __DIR__ . '/../vendor/autoload.php';

App::get('/request.php', function(){
  $page = App::request('page', 'empty');
  // or
  // $page = App::request()->get('page', 5);

  $postData = App::request()->only('title', 'body', 'tags');
  $title = App::request()->post('title');
  $image = App::request()->file('image');

  echo "Current page: $page";
});

App::any('/request.php/test', function(){
  if (App::request()->method('get')) {
    echo 'Method is GET';
  }

  if (App::request()->isAjax()) {
    echo 'Ajax request';
  }

  if (App::request()->method() === 'POST') {
    echo 'Request method is POST';
  }
  echo '<br>';
  echo App::request()->uri(); // /request.php/test
});

App::run();