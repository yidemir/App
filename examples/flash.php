<?php

use Demir\App;

require __DIR__ . '/../vendor/autoload.php';

App::session()->start();

App::get('/flash.php', function(){
  App::flash()->error('Error 1');
  App::flash()->error('Error 2');
  App::flash('Error 3', 'error'); // error type
  App::flash('Message 1', 'message'); // message type
  App::flash('Message 2'); // message type default
  echo '<a href="' . App::url('page') . '">Click here</a>';

}, 'homepage');

App::get('/flash.php/page', function(){
  var_dump(
    array_merge(
      App::flash()->getErrors(),
      App::flash()->getMessages()
    )
  );
}, 'page');

App::run();