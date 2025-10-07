<?php

use App\Http\Controllers\api;

$root = $_SERVER['DOCUMENT_ROOT'];
$file = file_get_contents($root . '/mailers/OTPmail.html', 'r');

$file = str_replace('#otp', $data['otp'], $file);
$file = str_replace('#name', $data['vendorname'], $file);

echo $file;
// exit();
?>
