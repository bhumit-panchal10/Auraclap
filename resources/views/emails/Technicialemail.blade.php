<?php

use App\Http\Controllers\api;

$root = $_SERVER['DOCUMENT_ROOT'];
$file = file_get_contents($root . '/mailers/Technicialemail.html', 'r');

$file = str_replace('#name', $data['contactname'], $file);

echo $file;
// exit();
?>
