<?php
$host = 'localhost';
$username = 'root';
$password = ''; 
$database = 'library-mgmt';
$port = 3309; 

$conn = mysqli_connect($host, $username, $password, $database, $port);

// if (!$conn) {
//     die("Connection failed: " . mysqli_connect_error());
// }

// echo "Connected successfully!";
?>