<?php
// Actual production database configuration
$servername = "ls-d569aff0f012a672548dfa141c2ff6da29f4a426.cx4g6oyqyv3o.ap-southeast-2.rds.amazonaws.com"; // Change if necessary
$username = "dbmasteruser"; // Change if necessary
$password = ",urI{;P+E-97C<Is>,AZkgnDZ5V313mw"; // Change if necessary
$dbname = "dbmaster"; // Change to your actual database name

//Check and change the configuration to test_db during test
$isTestMode = $_COOKIE['TEST_MODE'] ?? $_SERVER['HTTP_X_TEST_MODE'] ?? false;
if ($isTestMode === 'true'){
    include ('tests/Support/Data/test_db.php');
}

else {$conn = new mysqli($servername, $username, $password, $dbname);};//Actual production connection

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
