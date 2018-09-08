<?php
declare(strict_types=1);

use Demir\App;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase
{
  public function setUp()
  {
    parent::setUp();

    App::get('/', function(){
      echo 'get';
    }, 'home.get');

    App::post('/', function(){
      echo 'post';
    }, 'home.post');

    App::put('/', function(){
      echo 'put';
    }, 'home.put');

    App::patch('/', function(){
      echo 'patch';
    }, 'home.patch');

    App::delete('/', function(){
      echo 'delete';
    }, 'home.delete');

    App::get('/middleware', 'foo', 'middleware.route', [function(){
      if (true) exit('middleware');
    }]);

    App::get('/posts', 'PostController:index', 'posts.index');
    App::get('/posts/show/:id', 'PostController:show', 'posts.show');
  }

  public function testAddNewRoute() : void
  {
    $this->assertInstanceOf(
      App::class,
      App::get('/test', 'test')
    );
  }

  public function testUrlGeneratorMethod() : void
  {
    $this->assertEquals(
      App::url('posts.index'), '/posts'
    );
  }

  public function testCallUserFuncMethod() : void
  {
    $callable = function(){
      return 'callable';
    };

    $this->assertEquals(
      App::callUserFunc($callable, []), 'callable'
    );
  }

  public function testUrlGeneratorMethodWithParameters() : void
  {
    $this->assertEquals(
      App::url('posts.show', 10), '/posts/show/10'
    );
  }

  private function runAndGetResult($uri, $method)
  {
    ob_start();
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['REQUEST_METHOD'] = $method;
    App::run();
    return ob_get_clean();
  }

  public function testRouteResultGet() : void
  {
    $result = $this->runAndGetResult('/', 'GET');
    $this->assertEquals($result, 'get');
  }

  public function testRouteResultPost() : void
  {
    $result = $this->runAndGetResult('/', 'POST');
    $this->assertEquals($result, 'post');
  }

  public function testRouteResultPatch() : void
  {
    $result = $this->runAndGetResult('/', 'PATCH');
    $this->assertEquals($result, 'patch');
  }

  public function testRouteResultPut() : void
  {
    $result = $this->runAndGetResult('/', 'PUT');
    $this->assertEquals($result, 'put');
  }

  public function testRouteResultDelete() : void
  {
    $result = $this->runAndGetResult('/', 'DELETE');
    $this->assertEquals($result, 'delete');
  }

  public function testRouteDispatch() : void
  {
    $route = App::dispatch();
    $this->assertTrue(
      is_array($route) && 
      isset($route['methods']) &&
      isset($route['path']) &&
      isset($route['callback']) &&
      isset($route['name']) &&
      isset($route['middlewares']) &&
      isset($route['params'])
    );
  }
}