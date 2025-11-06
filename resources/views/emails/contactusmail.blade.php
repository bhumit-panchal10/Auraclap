<?php
$root = $_SERVER['DOCUMENT_ROOT'];
$file = file_get_contents($root . '/mailers/contactmail.html', 'r');

$file = str_replace('#FirstName', $data['FirstName'], $file);
$file = str_replace('#LastName', $data['LastName'], $file);
$file = str_replace('#Service', $data['Service'], $file);
$file = str_replace('#email', $data['Email'], $file);
$file = str_replace('#mobile', $data['Mobile'], $file);
$file = str_replace('#message', $data['Message'], $file);

echo $file;
?>
