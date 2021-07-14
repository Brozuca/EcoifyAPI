<?php
$db_host        = 'localhost';
$db_user        = 'f0523531_ecoify';
$db_pass        = 'CJw2LfZo';
$db_database    = 'f0523531_ecoify';

$connect = new mysqli($db_host, $db_user, $db_pass, $db_database);

if (mysqli_connect_errno()) {
    printf("Не удалось подключиться: %s\n", mysqli_connect_error());
    exit();
}

