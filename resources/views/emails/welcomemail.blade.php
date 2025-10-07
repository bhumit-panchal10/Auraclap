<?php

use App\Http\Controllers\api;

$root = $_SERVER['DOCUMENT_ROOT'];
$file = file_get_contents($root . '/mailers/welcomemail.html', 'r');

$file = str_replace('#name', $data['name'], $file);

echo $file;
// exit();
?>
