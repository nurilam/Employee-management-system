<?php
// connection.php
$dbhost = "127.0.0.1:3306";
$dbuser = "u428708477_DrDannvetAdmin";
$dbpass = "MyPKPIM123@";
$dbname = "u428708477_DrDannvetAdmin";

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}


?>
