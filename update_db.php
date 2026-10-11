<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

if (!isset($conn) \vert{}\vert{}$conn->connect_error) {
    die("<h2 style='color:red;'>Database Connection Failed. Check db.php credentials.</h2>");
}

// 1. Ensure the users table exists first
$createTableSQL = "
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$conn->query($createTableSQL);

// 2. List of columns needed for registration and KYC
$columns = [
    "firstname"      => "VARCHAR(100) DEFAULT NULL",
    "middlename"     => "VARCHAR(100) DEFAULT NULL",
    "lastname"       => "VARCHAR(100) DEFAULT NULL",
    "username"       => "VARCHAR(100) DEFAULT NULL",
    "phone"          => "VARCHAR(50) DEFAULT NULL",
    "dob"            => "DATE DEFAULT NULL",
    "ssn"            => "VARCHAR(100) DEFAULT NULL",
    "id_number"      => "VARCHAR(100) DEFAULT NULL",
    "street_address" => "VARCHAR(255) DEFAULT NULL",
    "city"           => "VARCHAR(100) DEFAULT NULL",
    "state"          => "VARCHAR(100) DEFAULT NULL",
    "zip_code"       => "VARCHAR(50) DEFAULT NULL",
    "country"        => "VARCHAR(100) DEFAULT NULL",
    "currency"       => "VARCHAR(20) DEFAULT 'USD'",
    "id_front"       => "VARCHAR(255) DEFAULT NULL",
    "id_back"        => "VARCHAR(255) DEFAULT NULL",
    "id_selfie"      => "VARCHAR(255) DEFAULT NULL",
    "address_doc"    => "VARCHAR(255) DEFAULT NULL"
];

echo "<div style='font-family: Arial, sans-serif; padding: 25px; max-width: 650px; margin: 40px auto; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);'>";
echo "<h2 style='color: #2c5364; margin-top: 0;'>Updating Database Schema...</h2><ul style='line-height: 1.8;'>";

foreach ($columns as$col => $definition) {$sql = "ALTER TABLE `users` ADD COLUMN `$col` $definition";
    
    // Using @ suppresses duplicate column errors if it already exists
    if (@$conn->query($sql)) {
        echo "<li style='color: green;'>Added column: <strong>$col</strong></li>";
    } else {
        echo "<li style='color: #777;'>Column already exists (skipped): <strong>$col</strong></li>";
    }
}

echo "</ul>";
echo "<h3 style='color: green; margin-top: 20px;'>Database sync complete!</h3>";
echo "<a href='signup.php' style='display: inline-block; background: #2c5364; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Return to Registration Form</a>";
echo "</div>";
?>
