<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

$sql = "
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS username VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS phone VARCHAR(50) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS dob DATE DEFAULT NULL,
ADD COLUMN IF NOT EXISTS ssn VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS id_number VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS id_front VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS id_back VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS id_selfie VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS address_doc VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS currency VARCHAR(20) DEFAULT 'USD',
ADD COLUMN IF NOT EXISTS country VARCHAR(100) DEFAULT NULL;
";

if ($conn->multi_query($sql)) {
    echo "<h2 style='color:green;'>Database tables and columns updated successfully!</h2>";
    echo "<p><a href='signup.php'>Go to Sign Up Page</a></p>";
} else {
    echo "<h2 style='color:red;'>Error updating database: " . $conn->error . "</h2>";
}
?>
