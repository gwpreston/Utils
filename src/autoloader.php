<?php

spl_autoload_register(function ($className) {
  $filePath = realpath(dirname(__FILE__)) . '/' . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
  if(file_exists($filePath) && !class_exists($filePath))
    include_once ($filePath);
});

?>
