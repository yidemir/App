<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title><?= $this->has('title') ? $this->get('title') : 'Hello from view' ?></title>
</head>
<body>
  <?= $this->get('content') ?>
  <?= $this->get('scripts') ?>
</body>
</html>