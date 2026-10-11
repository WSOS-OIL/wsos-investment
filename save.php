<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

if (!isset($conn) \vert{}\vert{}$conn->connect_error) {
    die("Error: Database connection is not available. Check db.php.");
}

// ==========================================
// SELF-HEALING DATABASE SYNC
// Automatically ensures all required columns exist 
// before processing the form, eliminating "Unknown column" errors.
// ==========================================
$required_columns = [
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

foreach ($required_columns as $col =>$definition) {
    @$conn->query("ALTER TABLE `users` ADD COLUMN `$col` $definition");
}

// ==========================================
// PROCESS REGISTRATION FORM
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect and sanitize inputs
    $firstname        = trim($_POST['firstname'] ?? '');
    $middlename       = trim($_POST['middlename'] ?? '');
    $lastname         = trim($_POST['lastname'] ?? '');
    $fullname         = trim($firstname . ' ' . $middlename . ' ' .$lastname);
    $username         = trim($_POST['username'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $phone            = trim($_POST['phone'] ?? '');
    $dob              = trim($_POST['dob'] ?? '');
    $ssn              = trim($_POST['ssn'] ?? '');
    $id_number        = trim($_POST['id_number'] ?? '');
    
    $street_address   = trim($_POST['street_address'] ?? '');
    $city             = trim($_POST['city'] ?? '');
    $state            = trim($_POST['state'] ?? '');
    $zip_code         = trim($_POST['zip_code'] ?? '');
    $country          = trim($_POST['country'] ?? '');
    
    $currency         = trim($_POST['currency'] ?? 'USD');
    $password         = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // 2. Validation
    if (empty($email) \vert{}\vert{} empty($password)) {
        die("Error: Email and password are required.");
    }

    if ($password !==$confirm_password) {
        die("Error: Passwords do not match. Please go back and try again.");
    }

    // 3. Setup Uploads Directory
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // 4. File Upload Helper Function
    function uploadFile($fileInputName,$upload_dir) {
        if (isset($_FILES[$fileInputName]) &&$_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {$fileTmpPath   = $_FILES[$fileInputName]['tmp_name'];
            $fileName      =$_FILES[$fileInputName]['name'];$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));$allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
            if (in_array($fileExtension, $allowedExtensions)) {$newFileName = md5(time() . $fileName) . '.' .$fileExtension;
                $dest_path   = $upload_dir .$newFileName;
                
                if (move_uploaded_file($fileTmpPath,$dest_path)) {
                    return $dest_path; 
                }
            }
        }
        return '';
    }

    $id_front_path    = uploadFile('id_front',$upload_dir);
    $id_back_path     = uploadFile('id_back',$upload_dir);
    $id_selfie_path   = uploadFile('id_selfie',$upload_dir);
    $address_doc_path = uploadFile('address_doc',$upload_dir);

    // 5. Insert into Database
    $stmt =$conn->prepare("INSERT INTO users (firstname, middlename, lastname, fullname, username, email, phone, dob, ssn, id_number, street_address, city, state, zip_code, country, id_front, id_back, id_selfie, address_doc, currency, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("sssssssssssssssssssss", $firstname, $middlename,$lastname, $fullname,$username, $email,$phone, $dob,$ssn, $id_number,$street_address, $city,$state, $zip_code,$country, $id_front_path,$id_back_path, $id_selfie_path,$address_doc_path, $currency,$password);

        if ($stmt->execute()) {
            header("Location: signin.php?registration=success");
            exit();
        } else {
            echo "Database Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Prepare Statement Error: " . $conn->error;
    }
} else {
    header("Location: signup.php");
    exit();
}
?>
