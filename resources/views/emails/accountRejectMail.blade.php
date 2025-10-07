<?php

$root = $_SERVER['DOCUMENT_ROOT'];
$file = file_get_contents($root . '/mailers/accountRejected.html', 'r');

echo $file;
?>
