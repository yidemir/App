# DemirApp Minimal PHP Framework

## Introduction
Simple and minimal yet another PHP 7 Framework

## Features
* Simple routing
* Simple container (Dependency Injection)
* Flash messages
* Simple JSON response
* Simple middleware system
* Resource routes
* Native PHP templating system
* and much more

Documentation is coming soon.
Türkçe dökümantasyon yakında yayınlanacak.

## Installation
The recommended way to install is via Composer:
```
composer require yidemir/app
```
and start coding:
```php
<?php

use Demir\App;

require 'vendor/autoload.php';

App::get('/', function(){
  return App::render('home');
});

App::run();
```

Or download from relases [page](https://github.com/yidemir/app/releases).
```php
<?php

use Demir\App;

require 'src/App.php';

App::get('/', function(){
  echo 'Hello world!';
});

App::run();
```

## Test
```
composer test
```