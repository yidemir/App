<?php
declare(strict_types=1);

use Demir\App;
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
  public function __construct()
  {
    parent::__construct();
    App::session()->start();
  }

  public function testSessionSet() : void
  {
    $set = App::session()->set('foo', 'bar');
    $this->assertEquals($set, 'bar');
  }

  public function testSessionGet() : void
  {
    $this->assertEquals(
      'bar', App::session()->get('foo')
    );
  }

  public function testFlashMessage() : void
  {
    App::flash()->message('ok');
    $messages = App::flash()->getMessages();
    $this->assertEquals($messages, ['ok']);
  }

  public function testFlashError() : void
  {
    App::flash()->error('error');
    $errors = App::flash()->getErrors();
    $this->assertEquals($errors, ['error']);
  }
}