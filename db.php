<?php
$servername = "ls-d569aff0f012a672548dfa141c2ff6da29f4a426.cx4g6oyqyv3o.ap-southeast-2.rds.amazonaws.com";
$username = "dbmasteruser"; 
$password = ",urI{;P+E-97C<Is>,AZkgnDZ5V313mw"; 
$dbname = "dbmaster"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
