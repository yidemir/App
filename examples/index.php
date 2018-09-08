<?php

foreach (glob(__DIR__ . '/*.php') as $file) {
  $file = explode('/', $file);
  $file = end($file);
  echo "<a href=\"{$file}\">{$file}</a><br>";
}