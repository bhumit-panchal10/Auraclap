<?php

$root = $_SERVER['DOCUMENT_ROOT'];
$file = file_get_contents($root . '/mailers/customerforgotPassword.html', 'r');

$file = str_replace('#otp', $data['otp'], $file);
$file = str_replace('#Customername', $data['Customer_name'], $file);

echo $file;
?>
