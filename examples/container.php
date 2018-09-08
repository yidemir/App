<?php

use Demir\App;

require __DIR__ . '/../vendor/autoload.php';

class Foo{}

App::container()->set('item', function(){
  return new Foo();
});

App::container(['item' => function(){
  return new Foo();
}]);

$item = App::container()->get('item');
$item = App::container('item');

App::container()->singleton('database', function(){
  return new \PDO('sqlite:database.sqlite');
});

// with parameter
App::container()->set('Greeting', function($container, $name){
  return new class($name) {
    public function __construct($name)
    {
      echo "Hello {$name}!";
    }
  };
});


App::container()->call('Greeting', ['Yılmaz']);
