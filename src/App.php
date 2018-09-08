<?php

namespace Demir;

/**
 * Class App
 *
 * @package Demir
 * @version 0.1.0
 * @author Yılmaz Demir <demiriy@gmail.com>
 * @license MIT
 * @see https://yilmazdemir.com.tr/demir-app-framework
 * @see https://github.com/yidemir/App
 *
 * @method static any()
 * @method static get()
 * @method static post()
 * @method static patch()
 * @method static put()
 * @method static delete()
 */
final class App
{
  /**
   * @static array
   */
  protected static $routes = [];

  /**
   * @static array
   */
  protected static $errors = [
    404 => null,
    500 => null
  ];

  /**
   * @static array
   */
  protected static $group = [
    'name' => null,
    'path' => null,
    'middlewares' => []
  ];

  /**
   * @static array
   */
  protected static $patterns = [
    ':number' => '(\d+)',
    ':id' => '(\d+)',
    ':string' => '([\wÜĞİŞÇÖıüğşçö%-_]+)',
    ':slug' => '([\w\-_]+)',
    ':any' => '([^/]+)',
    ':all' => '(.*)',
  ];

  /**
   * @param string|array $methods
   * @param string $path
   * @param string|callable $callback
   * @param string|null $name
   * @param array $middlewares
   * @return static
   */
  public static function map(
    $methods,
    string $path,
    $callback,
    $name = null,
    array $middlewares = []
  )
  {
    $path = static::$group['path'] . $path;
    $path = ($path === '/') ? '/' : rtrim($path, '/');
    $name = $name ? static::$group['name'] . $name : $name;
    $middlewares = array_merge(
      $middlewares, static::$group['middlewares']
    );

    array_push(
      static::$routes,
      [$methods, $path, $callback, $name, $middlewares]
    );

    return new static();
  }

  /**
   * @param string $name
   * @param array $args
   * @throws \InvalidArgumentException
   * @return static
   */
  public static function __callStatic(string $name, array $args)
  {
    if (in_array($name, ['any', 'get', 'post', 'put', 'patch', 'delete'])) {
      return static::map(strtoupper($name), ...$args);
    }

    throw new \InvalidArgumentException("Geçersiz istek: $name");
  }

  /**
   * @param string $name
   * @param array $args
   * @return static
   * @throws \Exception
   */
  public function __call(string $name, array $args)
  {
    return static::__callStatic($name, $args);
  }

  /**
   * @param string $path
   * @param callable $callback
   * @param string|null $name
   * @param array $middlewares
   */
  public static function group(
    string $path,
    callable $callback,
    string $name = null,
    array $middlewares = []
  )
  {
    static::$group['path'] .= $path;
    static::$group['name'] .= $name;
    static::$group['middlewares'] = array_merge(
      static::$group['middlewares'], $middlewares
    );
    call_user_func($callback);
    static::$group['path'] = null;
    static::$group['name'] = null;
    static::$group['middlewares'] = [];
  }

  /**
   * @param string|callable $callback
   */
  public static function notFound($callback = null)
  {
    if (is_null($callback)) {
      http_response_code(404);
      if (!is_null(static::$errors[404])) {
        static::callUserFunc(static::$errors[404]);
        exit();
      }
    } else {
      static::$errors[404] = $callback;
    }
  }

  /**
   * @param string|callable $callback
   */
  public static function error($callback)
  {
    static::$errors[500] = $callback;
  }

  /**
   * @param string $path
   * @param mixed $class
   * @param array $middlewares
   * @return static
   */
  public static function resource(string $path, $class, array $middlewares = [])
  {
    if (is_string($class)) {
      $index = "{$class}:index";
      $create = "{$class}:create";
      $store = "{$class}:store";
      $show = "{$class}:show";
      $edit = "{$class}:edit";
      $update = "{$class}:update";
      $destroy = "{$class}:destroy";
    } elseif (is_object($class) && !$class instanceof \Closure) {
      $index = [$class, 'index'];
      $create = [$class, 'create'];
      $store = [$class, 'store'];
      $show = [$class, 'show'];
      $edit = [$class, 'edit'];
      $update = [$class, 'update'];
      $destroy = [$class, 'destroy'];
    }

    $path = ($path === '/') ? '/' : rtrim($path, '/');
    $name = str_replace('/', '.', trim($path, '/'));
    static::get($path, $index, "{$name}.index", $middlewares);
    static::get("$path/create", $create, "{$name}.create", $middlewares);
    static::post($path, $store, "{$name}.store", $middlewares);
    static::get("$path/:id", $show, "{$name}.show", $middlewares);
    static::get("$path/:id/edit", $edit, "{$name}.edit", $middlewares);
    static::patch("$path/:id", $update, "{$name}.update", $middlewares);
    static::delete("$path/:id", $destroy, "{$name}.destroy", $middlewares);

    return new static();
  }

  /**
   * @param string $name
   * @param array $args
   * @return string
   * @throws \Exception
   */
  public static function url(string $name, ...$args) : string
  {
    foreach (static::$routes as $route) {
      list(, $path, , $routeName, ) = $route;
      if ($routeName == $name) {
        preg_match_all('/(:[a-zA-Z]+)/i', $path, $matches);
        return count($matches[0]) == count($args) ?
          strtr($path, array_combine($matches[0], $args)) : $path;
      }
    }

    throw new \Exception("Böyle bir rota ismi yok: '{$name}'");
  }


  /**
   * @param string $route
   * @throws \Exception
   */
  public static function redirect(string $route)
  {
    $url = static::url($route);
    header("Location: {$url}");
  }

  /**
   * @return array
   */
  public static function dispatch()
  {
    foreach (static::$routes as $route) {
      list($methods, $path, , , ) = $route;

      $checkMethod = is_array($methods) ?
        (in_array(static::request()->method(), $methods)) :
        (static::request()->method() === $methods || $methods === 'ANY');

      $path = strtr($path, static::$patterns);
      $checkPath = preg_match("@^$path$@ixs", static::request()->uri(), $matches);

      if ($checkMethod && $checkPath > 0) {
        array_shift($matches);

        return [
          'methods' => $route[0],
          'path' => $route[1],
          'callback' => $route[2],
          'name' => $route[3],
          'middlewares' => $route[4],
          'params' => $matches
        ];
      }
    }

    return [
      'methods' => null,
      'path' => null,
      'callback' => static::$errors[404],
      'name' => '404',
      'middlewares' => [],
      'params' => []
    ];
  }

  /**
   * @return mixed
   */
  public static function run()
  {
    if (static::container('debug', false) === true) {
      ini_set('display_errors', 1);
      error_reporting(E_ALL);
    } else {
      ini_set('display_errors', 0);
      error_reporting(0);
    }

    if (!is_null(static::$errors[500])) {
      set_exception_handler(static::$errors[500]);
    }

    if (is_null(static::$errors[404])) {
      static::notFound(function(){
        http_response_code(404);
        echo '<h2>404 Not Found</h2>';
      });
    }

    $route = static::dispatch();
    return static::callUserFunc(
      $route['callback'],
      $route['params'] ?: [],
      $route['middlewares'] ?: []
    );
  }

  /**
   * @param mixed $callable
   * @param array $params
   * @param array $middlewares
   * @throws \InvalidArgumentException
   * @return mixed
   */
  public static function callUserFunc(
    $callable,
    array $params = [],
    array $middlewares = []
  )
  {
    $pattern = '!^([^\:]+)\:([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$!';

    foreach ($middlewares as $middleware) {
      call_user_func($middleware);
    }

    if (is_string($callable) && preg_match($pattern, $callable, $matches)) {
      list(, $class, $method) = $matches;

      if (!class_exists($class)) {
        throw new \InvalidArgumentException(
          "Bu isimde bir kontrolcü yok '{$class}'"
        );
      }

      $callable = function() use ($class, $method, $params) {
        static $object = null;

        if (is_null($object)) {
            $object = new $class;
        }

        if (!method_exists($object, $method)) {
          throw new \InvalidArgumentException(
            "Bu isimde bir metod yok '{$method}'"
          );
        }

        return call_user_func_array([$object, $method], $params);
      };
    }

    if (!is_callable($callable)) {
      throw new \InvalidArgumentException(
        'Rota değeri çağırılabilir olmalıdır'
      );
    }

    return call_user_func_array($callable, $params);
  }

  /**
   * @param array $data
   * @return int
   */
  public static function json(array $data)
  {
    header('Content-Type: application/json;charset=utf-8');
    return print json_encode($data);
  }

  /**
   * @param string $name
   * @param array $data
   * @return object
   */
  public static function view(string $name, array $data = [])
  {
    $path = static::container('views.path', __DIR__.'/views');

    return new class($name, $data, $path) {
      /**
       * @var string
       */
      protected $name;

      /**
       * @var array
       */
      protected $data;

      /**
       * @var string
       */
      protected $path;

      /**
       * @var string
       */
      protected $actualBlock;

      /**
       * @var string
       */
      protected $layout;

      /**
       * @var array
       */
      protected $blocks = [];

      /**
       * @param string $name
       * @param array $data
       * @param string $path
       */
      public function __construct(
        string $name,
        array $data,
        string $path
      )
      {
        $this->name = $name;
        $this->data = $data;
        $this->path = $path;
      }

      /**
       * @return string
       * @throws \Exception
       */
      public function render()
      {
        if ($file = $this->exists($this->name)) {
          $this->set('content');
          extract($this->data);
          require $file;
          $this->actualBlock = 'content';
          $this->end();

          if (is_null($this->layout)) {
            ob_start();
            extract($this->data);
            require $file;
            return ob_get_clean();
          } else {
            if ($layout = $this->exists($this->layout)) {
              ob_start();
              require $layout;
              return ob_get_clean();
            }

            throw new \Exception(
              "Görünüm yerleştirme dosyası bulunamadı '{$layout}'"
            );
          }
        }

        throw new \Exception(
          "Böyle bir görünüm dosyası mevcut değil: {$this->name}"
        );
      }

      /**
       * @return int
       */
      public function show()
      {
        return print $this->render();
      }

      /**
       * @param array|string $key
       * @param mixed $value
       * @return object
       */
      public function with($key, $value = null)
      {
        if (is_array($key)) {
          $this->data = array_merge($this->data, $key);
        } else {
          $this->data[$key] = $value;
        }

        return $this;
      }

      /**
       * @return string
       */
      public function __toString()
      {
        return $this->render();
      }

      /**
       * @param string $name
       * @return string|bool
       */
      public function exists(string $name)
      {
        return is_file($path = "{$this->path}/{$name}.php") ?
          $path : false;
      }

      /**
       * @param string $layout
       */
      public function layout(string $layout = 'layout')
      {
        $this->layout = $layout;
      }

      /**
       * @param string $name
       * @param mixed $data
       */
      public function start(string $name, $data = null)
      {
        if (!is_null($data)) {
          $this->blocks[$name] = $data;
        } else {
          $this->actualBlock = $name;
          ob_start();
        }
      }

      /**
       * @param string $name
       * @param mixed $data
       * @return void
       */
      public function set(string $name, $data = null, bool $append = false)
      {
        if ($append) {
          if (!$this->has($name)) {
            $this->blocks[$name] = '';
          }

          $this->blocks[$name] .= $data;
        } else {
          $this->blocks[$name] = $data;
        }
      }

      /**
       * @return void
       */
      public function end($append = true)
      {
        if ($append) {
          if (!$this->has($this->actualBlock)) {
            $this->blocks[$this->actualBlock] = '';
          }

          $this->blocks[$this->actualBlock] .= ob_get_clean();
        } else {
          $this->blocks[$this->actualBlock] = ob_get_clean();
        }
        
        $this->actualBlock = null;
      }

      /**
       * @param string $name
       * @return bool
       */
      public function has(string $name) : bool
      {
        return array_key_exists($name, $this->blocks);
      }

      /**
       * @param string $name
       * @param mixed $default
       * @return mixed
       */
      public function get(string $name, $default = null)
      {
        return $this->has($name) ?
          $this->blocks[$name] : $default;
      }
    };
  }

  /**
   * @param string $name
   * @param array $data
   * @return int
   */
  public static function render(string $name, array $data = [])
  {
    return static::view($name, $data)->show();
  }

  /**
   * @param mixed $name
   * @param mixed $value
   * @return object
   */
  public static function container($name = null, $value = null)
  {
    if (!is_null($name) && is_array($name)) {
      return static::container()->set($name);
    } elseif (!is_null($name) && is_string($name)) {
      return static::container()->get($name, $value);
    }

    return new class {
      /**
       * @var array
       */
      protected static $container = [];

      /**
       * @param string $name
       * @return bool
       */
      public static function has(string $name) :  bool
      {
        return array_key_exists($name, static::$container);
      }

      /**
       * @param string $name
       * @param array $args
       * @return mixed
       */
      public static function call(string $name, array $args = [])
      {
        if (static::has($name) && is_callable(static::$container[$name])) {
          array_unshift($args, static::$container);
          return call_user_func_array(
            static::$container[$name], $args
          );
        }

        throw new \Exception("Öğe yok ya da çağırılabilir değil: '{$name}'");
      }

      /**
       * @param string $name
       * @param mixed $default
       * @return mixed
       */
      public static function get(string $name, $default = null)
      {
        if (static::has($name)) {
          return is_callable(static::$container[$name]) ?
            call_user_func_array(static::$container[$name], [static::$container]) :
            static::$container[$name];
        }

        return $default;
      }

      /**
       * @param string $name
       * @param mixed $value
       * @return void
       */
      public static function set($name, $value = null)
      {
        if (is_array($name)) {
          foreach ($name as $k => $v) {
            static::$container[$k] = $v;
          }
        } else {
          static::$container[$name] = $value;
        }
      }

      /**
       * @param string $name
       * @param callable $value
       * @return void
       */
      public static function singleton(string $name, callable $value)
      {
        static::set($name, function() use ($value){
          static $object;
          if (is_null($object)) $object = $value(new static());
          return $object;
        });
      }
    };
  }

  /**
   * @param mixed $key
   * @param mixed $default
   * @return mixed
   */
  public static function request($key = null, $default = null)
  {
    if (!is_null($key) && is_string($key)) {
      return static::request()->param($key, $default);
    } elseif (!is_null($key) && is_array($key)) {
      return static::request()->only($key);
    }

    return new class {
      /**
       * @return array
       */
      public static function params() : array
      {
        return $_REQUEST;
      }

      /**
       * @return array
       */
      public static function all() : array
      {
        return static::params();
      }

      /**
       * @param string $key
       * @param mixed $default
       * @return mixed
       */
      public static function param(string $key, $default = null)
      {
        $params = static::params();
        return static::has($key) ? $params[$key] : $default;
      }

      /**
       * @param array $keys
       * @return array
       */
      public static function only(...$keys) : array
      {
        $result = [];
        foreach ($keys as $key) {
          if (static::has($key)) {
            $result[$key] = static::param($key);
          }
        }
        return $result;
      }

      /**
       * @param string $key
       * @return bool
       */
      public static function has(string $key) : bool
      {
        return array_key_exists($key, static::params());
      }

      /**
       * @param string $key
       * @param mixed $default
       * @return mixed
       */
      public static function get(string $key, $default = null)
      {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
      }

      /**
       * @param string $key
       * @param mixed $default
       * @return mixed
       */
      public static function post(string $key, $default = null)
      {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
      }

      /**
       * @param string $key
       * @param mixed $default
       * @return mixed
       */
      public static function file(string $key, $default = null)
      {
        return isset($_FILES[$key]) ? $_FILES[$key] : $default;
      }

      /**
       * @return string
       */
      public function uri() : string
      {
        return $_SERVER['REQUEST_URI'];
      }

      /**
       * @param string|null $is
       * @return string|bool
       */
      public function method($is = null)
      {
        if (isset($_POST['_method'])) {
          $method = strtoupper($_POST['_method']);
        }

        if (isset($_SERVER['REQUEST_METHOD'])) {
          $method = $_SERVER['REQUEST_METHOD'];
        } else {
          $method = 'GET';
        }

        if (!is_null($is)) {
          return $method === strtoupper($is);
        }

        return $method;
      }

      /**
       * @return bool
       */
      public function isAjax()
      {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
      }
    };
  }

  /**
   * @return App
   */
  public static function getInstance()
  {
    return new static();
  }

  /**
   * @param mixed $key
   * @param mixed $default
   * @return object
   */
  public static function session($key = null, $default = null)
  {
    if (!is_null($key) && is_array($key)) {
      return static::session()->set($key);
    } elseif (!is_null($key) && is_string($key)) {
      return static::session()->get($key, $default);
    }

    return new class {
      /**
       * @return void
       */
      public static function start()
      {
        if (empty(session_id())) session_start();
      }

      /**
       * @param mixed $key
       * @param mixed $default
       * @return mixed
       */
      public static function get($key, $default = null)
      {
        return static::has($key) ? $_SESSION[$key] : $default;
      }

      /**
       * @param mixed $key
       * @param mixed $data
       * @return mixed
       * @throws \Exception
       */
      public static function set($key, $data = null)
      {
        if (!empty(session_id())) {
          if (is_array($key)) {
            foreach ($key as $k => $v) {
              $_SESSION[$k] = $v;
            }
            return $key;
          } else {
            return $_SESSION[$key] = $data;
          }
        }

        throw new \Exception('Session başlatılmamış');
      }

      /**
       * @param mixed $key
       * @return bool
       */
      public static function has($key) : bool
      {
        return array_key_exists($key, $_SESSION);
      }

      /**
       * @param mixed $key
       * @return bool
       */
      public static function destroy($key = null)
      {
        if (is_null($key)) {
          session_destroy();
        } else {
          unset($_SESSION[$key]);
        }

        return true;
      }

      /**
       * @param string|null $flash
       * @param string $type
       * @return mixed
       */
      public static function flash($flash = null, string $type = 'message')
      {
        if (!is_null($flash)) {
          return static::flash()->set($flash, $type);
        }

        return new class {
          /**
           * @param mixed $flash
           * @param string $type
           */
          public static function set($flash, string $type = 'message')
          {
            $_SESSION["_flash_{$type}"][] = $flash;
          }

          /**
           * @param mixed $flash
           * @return static
           */
          public static function error($flash)
          {
            return static::set($flash, 'error');
          }

          /**
           * @param mixed $flash
           * @return static
           */
          public static function message($flash)
          {
            return static::set($flash, 'message');
          }

          /**
           * @param string $type
           * @return array
           */
          public static function get(string $type = 'message')
          {
            if (isset($_SESSION["_flash_{$type}"])) {
              $result = $_SESSION["_flash_{$type}"];
              unset($_SESSION["_flash_{$type}"]);
            } else {
              $result = [];
            }

            return $result;
          }

          /**
           * @return array
           */
          public static function getErrors()
          {
            return static::get('error');
          }

          /**
           * @return mixed
           */
          public static function getMessages()
          {
            return static::get('message');
          }
        };
      }
    };
  }

  /**
   * @param mixed $flash
   * @param string $type
   * @return static
   */
  public static function flash($flash = null, string $type = 'message')
  {
    if (!is_null($flash)) {
      return static::session()->flash()->set($flash, $type);
    }

    return static::session()->flash();
  }
}
