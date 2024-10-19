<?php
// connection.php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "employee_management";

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
