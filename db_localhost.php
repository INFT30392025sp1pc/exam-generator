// Enable MySQLi error reporting for development
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database connection parameters
$host = "ls-d569aff0f012a672548dfa141c2ff6da29f4a426.cx4g6oyqyv3o.ap-southeast-2.rds.amazonaws.com";
$port = 3306;
$user = "dbmasteruser";
$password = ",urI{;P+E-97C<Is>,AZkgnDZ5V313mw"; 
$dbname = "Database-1";

// Create a new MySQLi connection
$con = new mysqli($host, $user, $password, $dbname, $port);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Connection successful
echo "Connected successfully";


