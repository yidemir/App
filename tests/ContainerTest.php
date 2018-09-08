<?php
declare(strict_types=1);

use Demir\App;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
  public function testContainerString() : void
  {
    $name = 'Demir';

    App::container()->set('name', $name);

    $this->assertEquals(
      App::container()->get('name'), $name
    );
  }

  public function testContainerCallback() : void
  {
    App::container()->set('callback', function(){
      return 'test';
    });

    $this->assertEquals(
      App::container()->get('callback'), 'test'
    );
  }

  public function testContainerClassWithParameter() : void
  {
    App::container()->set('greeting', function($container, $name){
      return new class($name) {
        protected $name;

        public function __construct($name)
        {
          $this->name = $name;
        }

        public function greet()
        {
          return "Hello $this->name";
        }
      };
    });

    $greeting = App::container()->call('greeting', ['Demir']);
    $this->assertEquals($greeting->greet(), 'Hello Demir');
  }

  public function testContainerSingleton()
  {
    $instance = $this;

    App::container()->singleton('testCase', function() use ($instance){
      return $instance;
    });

    App::container()->singleton('anotherTestCase', function($container) use ($instance){
      return $container->get('testCase') === $instance;
    });

    $this->assertTrue(
      App::container('anotherTestCase')
    );
  }


}