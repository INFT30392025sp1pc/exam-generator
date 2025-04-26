<?php
$host="ls-d569aff0f012a672548dfa141c2ff6da29f4a426.cx4g6oyqyv3o.ap-southeast-2.rds.amazonaws.com";
$port=3306;
$socket="";
$user="dbmasteruser";
$password="";
$dbname="dbmaster";

$con = new mysqli($host, $user, $password, $dbname, $port, $socket)
	or die ('Could not connect to the database server' . mysqli_connect_error());

//$con->close();
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
} else {
    //echo "Connected successfully";
}
?>
