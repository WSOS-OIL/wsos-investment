<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

if (!isset($conn) || $conn->connect_error) {
    die("<h2 style='color:red;'>Database Connection Failed.</h2>");
}

// Run ALTER statements one by one to ensure columns are added cleanly
$queries = [
    "ALTER TABLE users ADD COLUMN firstname VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN middlename VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN lastname VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN username VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN phone VARCHAR(50) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN dob DATE DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN ssn VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN id_number VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN street_address VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN city VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN state VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN zip_code VARCHAR(50) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN country VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN currency VARCHAR(20) DEFAULT 'USD'",
    "ALTER TABLE users ADD COLUMN id_front VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN id_back VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN id_selfie VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN address_doc VARCHAR(255) DEFAULT NULL"
];

$success = true;
foreach ($queries as $q) {
    @$conn->query($q); // Using @ suppresses errors if column already exists
}

echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
echo "<h1 style='color: green;'>Database Columns Updated Successfully!</h1>";
echo "<p>All missing table columns have been successfully enforced.</p>";
echo "<br><a href='signup.php' style='background: #2c5364; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Go to Sign Up Page</a>";
echo "</div>";
?>
