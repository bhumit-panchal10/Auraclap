<?php

$root = $_SERVER['DOCUMENT_ROOT'];
$file = file_get_contents($root . '/vybecab/mailers/investormail.html', 'r');

$file = str_replace('#strFullName#', $data['strFullName'], $file);
$file = str_replace('#strEmail', $data['strEmail'], $file);
$file = str_replace('#iMobile', $data['iMobile'], $file);
$file = str_replace('#iAlternativeMobile', $data['iAlternativeMobile'], $file);
$file = str_replace('#strDescription', $data['strDescription'], $file);
$file = str_replace('#strEntryDate', date('d-m-Y'), $file);

echo $file;

?>
