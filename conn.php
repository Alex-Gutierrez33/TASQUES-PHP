<?php

$servername = "localhost";
$username = "root";
$password = "Alex2310!";
$db = "agenda";

$conn = mysqli_connect($servername, $username, $password,$db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


?> 
