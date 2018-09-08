<?php

use Demir\App;

require __DIR__ . '/../vendor/autoload.php';

App::session()->start();

App::get('/session.php', function(){
  App::session(['foo' => 'bar']);
  // or
  App::session()->set('foo', 'bar');

  App::redirect('show.session.data');
});

App::get('/session.php/session', function(){
  echo App::session('foo'); // bar
  echo App::session()->get('foo'); // bar
}, 'show.session.data');

App::run();