<?php
// db config
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "cr7";

$conn = mysqli_connect($host,$user,$pass,$db_name);

if(!$conn){
    die("Connection failed: ".mysqli_connect_error());
}

// TODO: move this to .env later maybe
?>
