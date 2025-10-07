<?php

$root = $_SERVER['DOCUMENT_ROOT'];
$file = file_get_contents($root . '/mailers/offer.html', 'r');

$file = str_replace('#name', $data['firstName'] . ' ' . $data['lastName'], $file);
$file = str_replace('#email', $data['email'], $file);
$file = str_replace('#mobile', $data['phone_number'], $file);
$file = str_replace('#message', $data['message'], $file);
$file = str_replace('#strEntryDate', date('d-m-Y'), $file);

echo $file;
//dd($file);
?>
