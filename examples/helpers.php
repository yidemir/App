<?php

require __DIR__ . '/../vendor/autoload.php';

// routes
app()->get('/helpers.php', function(){
  container(['views.path' => __DIR__.'/views']);
  return render('home');
  // or 
  // return view('home')->with('title', 'Title')->show();
});

app()->get('/post/:id', function($id){
  echo "Post id: {$id}<br>";

  if ($page = request()->get('page')) {
    echo "Actual page: {$page}"; 
  }
}, 'show.post');

app()->any('/posts', function(){
  echo '<a href="' . url('show.post', 5) . '">Show post</a>';
});


// route groups
app()->group('/admin', function(){
  app()->get('/', function(){
    echo 'admin dashboard';
  }, 'home');

  app()->get('/dashboard', function(){
    return redirect('admin.home');
  }, 'dashboard');
}, 'admin.');


// session
session()->start();
session()->set('name', 'Demir'); // same as: session(['name' => 'Demir']);
session()->get('name'); // same as: session('name');


// flash
flash('Message');
flash()->error('Error');
flash()->getErrors();
flash()->get('error');
flash('Foo 1', 'notification');
flash('Foo 2', 'notification');
flash()->get('notification'); // ['Foo 1', 'Foo 2']


// url
url('admin.dashboard'); // /admin/dashboard
url('show.post', 51); // /post/51


// json
app()->get('/json', function(){
  return json(['data']);
});


// container
container()->set('foo', 'bar'); // container(['foo' => 'bar']);
container()->get('foo'); // container('foo');
container()->singleton('baz', function(){
  return new stdClass();
});
container()->call('baz');

run();