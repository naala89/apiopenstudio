<?php

$host = getenv('MYSQL_HOST');
$user = getenv('MYSQL_USER');
$pass = getenv('MYSQL_ROOT_PASSWORD');
 
$conn = mysqli_connect('compose-mysql', 'root', 'gaterdata');
if (!$conn) {
    exit('Connection failed: '.mysqli_connect_error().PHP_EOL);
}
 
echo 'Successful database connection!'.PHP_EOL;