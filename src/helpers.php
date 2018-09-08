<?php

if (!function_exists('app')) {
  /**
   * @return Demir\App
   */
  function app()
  {
    return \Demir\App::getInstance();
  }
}

if (!function_exists('run')) {
  /**
   * @return mixed
   */
  function run()
  {
    return app()->run();
  }
}

if (!function_exists('request')) {
  /**
   * @param mixed $key
   * @param mixed $default
   * @return mixed
   */
  function request($key = null, $default = null)
  {
    return app()->request($key, $default);
  }
}

if (!function_exists('view')) {
  /**
   * @param string $name
   * @param array $data
   * @return object
   */
  function view(string $name, array $data = [])
  {
    return app()->view($name, $data);
  }
}

if (!function_exists('render')) {
  /**
   * @param string $name
   * @param array $data
   * @return mixed
   */
  function render(string $name, array $data = [])
  {
    return view($name, $data)->show();
  }
}

if (!function_exists('session')) {
  /**
   * @param mixed $key
   * @param mixed $default
   * @return mixed
   */
  function session($key = null, $default = null)
  {
    return app()->session($key, $default);
  }
}

if (!function_exists('redirect')) {
  /**
   * @param string $route
   * @throws \Exception
   * @return void
   */
  function redirect(string $route)
  {
    return app()->redirect($route);
  }
}

if (!function_exists('flash')) {
  /**
   * @param mixed $flash
   * @param string $type
   * @return void
   */
  function flash($flash = null, string $type = 'message')
  {
    return session()->flash($flash, $type);
  }
}

if (!function_exists('url')) {
  /**
   * @param string $name
   * @param array $args
   * @throws \Exception
   * @return string
   */
  function url(string $name, ...$args)
  {
    return app()->url($name, ...$args);
  }
}

if (!function_exists('json')) {
  /**
   * @param array $data
   * @return int
   */
  function json(array $data)
  {
    return app()->json($data);
  }
}

if (!function_exists('container')) {
  /**
   * @param mixed $name
   * @param mixed $value
   * @return mixed
   */
  function container($name = null, $value = null)
  {
    return app()->container($name, $value);
  }
}