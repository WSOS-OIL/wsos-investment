<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

// Check if $conn is valid
if (!isset($conn) || $conn->connect_error) {
    die("<h2 style='color:red;'>Database Connection Failed: Check your db.php settings.</h2>");
}

$sql = "
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS firstname VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS middlename VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS lastname VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS username VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS phone VARCHAR(50) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS dob DATE DEFAULT NULL,
ADD COLUMN IF NOT EXISTS ssn VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS id_number VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS street_address VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS city VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS state VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS zip_code VARCHAR(50) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS country VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS currency VARCHAR(20) DEFAULT 'USD',
ADD COLUMN IF NOT EXISTS id_front VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS id_back VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS id_selfie VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS address_doc VARCHAR(255) DEFAULT NULL;
";

if ($conn->multi_query($sql)) {
    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
    echo "<h1 style='color: green;'>Database Updated Successfully!</h1>";
    echo "<p>All required columns for names, addresses, and KYC documents have been added to your database.</p>";
    echo "<br><a href='signup.php' style='background: #2c5364; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Go to Sign Up Page</a>";
    echo "</div>";
} else {
    echo "<h2 style='color: red;'>Error updating database: " . $conn->error . "</h2>";
}
?>
