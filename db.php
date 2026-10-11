<?php
// Database connection configuration
// If using environment variables on Render, it grabs them automatically, 
// otherwise fallback to your Clever Cloud credentials.
$host = getenv('DB_HOST') ?: 'bsupthtiiz0dktuoswnk-mysql.services.clever-cloud.com';
$user = getenv('DB_USER') ?: 'bsupthtiiz0dktuoswnk';
$pass = getenv('DB_PASS') ?: 'ugzbydhos9llph8w';
$dbname = getenv('DB_NAME') ?: 'qwYCSflpC6EHw2RzG5FA';
$port = getenv('DB_PORT') ?: 3306;

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");
?>
