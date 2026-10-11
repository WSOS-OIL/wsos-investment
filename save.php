<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect and sanitize text inputs
    $fullname         = trim($_POST['fullname'] ?? '');
    $username         = trim($_POST['username'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $phone            = trim($_POST['phone'] ?? '');
    $dob              = trim($_POST['dob'] ?? '');
    $ssn              = trim($_POST['ssn'] ?? '');
    $id_number        = trim($_POST['id_number'] ?? '');
    $currency         = trim($_POST['currency'] ?? 'USD');
    $country          = trim($_POST['country'] ?? '');
    $password         = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // 2. Basic Validation
    if (empty($email) || empty($password)) {
        die("Error: Email and password are required.");
    }

    if ($password !== $confirm_password) {
        die("Error: Passwords do not match. Please go back and try again.");
    }

    // 3. Setup Uploads Directory
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // 4. Helper Function to Handle File Uploads Safely
    function uploadFile($fileInputName, $upload_dir) {
        if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath   = $_FILES[$fileInputName]['tmp_name'];
            $fileName      = $_FILES[$fileInputName]['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
            if (in_array($fileExtension, $allowedExtensions)) {
                // Generate a unique filename to prevent overwriting
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path   = $upload_dir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    return $dest_path; // Returns relative path (e.g., uploads/abc123.jpg)
                }
            }
        }
        return '';
    }

    // Process all uploaded KYC files
    $id_front_path    = uploadFile('id_front', $upload_dir);
    $id_back_path     = uploadFile('id_back', $upload_dir);
    $id_selfie_path   = uploadFile('id_selfie', $upload_dir);
    $address_doc_path = uploadFile('address_doc', $upload_dir);

    // 5. Insert into Database
    $stmt = $conn->prepare("INSERT INTO users (fullname, username, email, phone, dob, ssn, id_number, id_front, id_back, id_selfie, address_doc, currency, country, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("ssssssssssssss", $fullname, $username, $email, $phone, $dob, $ssn, $id_number, $id_front_path, $id_back_path, $id_selfie_path, $address_doc_path, $currency, $country, $password);

        if ($stmt->execute()) {
            // Success: Redirect user to sign-in page
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
