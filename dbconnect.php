<?php

// Author: Abrar Mahmud


$server = "localhost";
$username = "root";
$password = "";
$dbname = "cse370_sec06_group02";

$conn = new mysqli($server, $username, $password);

if($conn->connect_error) {
    die("Connection to DB failed: " . $conn->connect_error);
} else {
    $conn->select_db($dbname);
}

?>