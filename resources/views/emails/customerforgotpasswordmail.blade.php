<?php

use App\Http\Controllers\api;

$root = $_SERVER['DOCUMENT_ROOT'];
$file = file_get_contents($root . '/mailers/customerforgotpasswordmail.html', 'r');

$file = str_replace('#password', $data['password'], $file);
$file = str_replace('#email', $data['Customer_email'], $file);
$file = str_replace('#name', $data['name'], $file);

echo $file;
// exit();
?>
