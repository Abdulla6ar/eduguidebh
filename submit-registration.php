<?php
/**
 * EduGuideBH Registration Form Submission Handler
 * This file receives form data and saves it to MySQL database
 */

// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php_errors.log');

// Set headers for CORS and JSON response
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit();
}

// Database configuration - UPDATE THESE VALUES
$db_config = [
    'host' => 'localhost',
    'database' => 'eduguidebh',  // Your existing database name
    'username' => 'YOUR_DB_USERNAME',  // Replace with your actual username
    'password' => 'YOUR_DB_PASSWORD',  // Replace with your actual password
    'charset' => 'utf8mb4'
];

// Upload directory configuration
$upload_dir = '/var/www/html/uploads/';  // Your Apache web root
$allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
$max_file_size = 10 * 1024 * 1024; // 10MB

/**
 * Connect to database
 */
function connectDatabase($config) {
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, $config['username'], $config['password'], $options);
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return null;
    }
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Handle file upload
 */
function handleFileUpload($file, $field_name, $student_id) {
    global $upload_dir, $allowed_extensions, $max_file_size;
    
    if (!isset($file['error']) || is_array($file['error'])) {
        return null;
    }
    
    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log("File upload error for {$field_name}: " . $file['error']);
        return null;
    }
    
    // Check file size
    if ($file['size'] > $max_file_size) {
        return null;
    }
    
    // Check extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_extensions)) {
        return null;
    }
    
    // Generate unique filename
    $unique_filename = sprintf(
        '%s_%s_%s.%s',
        $student_id,
        $field_name,
        uniqid(),
        $file_extension
    );
    
    $destination = $upload_dir . $unique_filename;
    
    // Create upload directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0775, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $unique_filename;
    }
    
    return null;
}

/**
 * Send email notification (optional)
 */
function sendEmailNotification($data) {
    $to = 'admin@eduguidebh.com'; // Replace with your email
    $subject = 'New Registration: ' . $data['full_name'];
    $message = "New registration received:\n\n";
    $message .= "Name: " . $data['full_name'] . "\n";
    $message .= "Email: " . $data['email'] . "\n";
    $message .= "Phone: " . $data['student_phone'] . "\n";
    $message .= "Program: " . $data['selected_program'] . "\n";
    $message .= "\nSubmission Date: " . date('Y-m-d H:i:s');
    
    $headers = 'From: noreply@eduguidebh.com' . "\r\n" .
               'Reply-To: ' . $data['email'] . "\r\n" .
               'X-Mailer: PHP/' . phpversion();
    
    @mail($to, $subject, $message, $headers);
}

// Main processing starts here
try {
    // Connect to database
    $pdo = connectDatabase($db_config);
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Get POST data
    $data = [
        'first_name' => sanitizeInput($_POST['firstName'] ?? ''),
        'father_name' => sanitizeInput($_POST['fatherName'] ?? ''),
        'grandfather_name' => sanitizeInput($_POST['grandfatherName'] ?? ''),
        'family_name' => sanitizeInput($_POST['familyName'] ?? ''),
        'full_name' => sanitizeInput($_POST['fullName'] ?? ''),
        'nationality' => sanitizeInput($_POST['nationality'] ?? ''),
        'address' => sanitizeInput($_POST['address'] ?? ''),
        'street_number' => sanitizeInput($_POST['streetNumber'] ?? ''),
        'house_number' => sanitizeInput($_POST['houseNumber'] ?? ''),
        'student_phone' => sanitizeInput($_POST['studentPhone'] ?? ''),
        'work_phone' => sanitizeInput($_POST['workPhone'] ?? ''),
        'email' => sanitizeInput($_POST['email'] ?? ''),
        'program_type' => sanitizeInput($_POST['programType'] ?? ''),
        'selected_program' => sanitizeInput($_POST['selectedProgram'] ?? ''),
        'additional_notes' => sanitizeInput($_POST['notes'] ?? '')
    ];
    
    // Validate required fields
    if (empty($data['first_name']) || empty($data['family_name']) || empty($data['email'])) {
        throw new Exception('Required fields are missing');
    }
    
    // Validate email
    if (!validateEmail($data['email'])) {
        throw new Exception('Invalid email address');
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM registrations WHERE email = ? LIMIT 1");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        throw new Exception('This email is already registered');
    }
    
    // Generate student ID
    $student_id = 'EDU' . date('Ymd') . sprintf('%04d', rand(1, 9999));
    
    // Handle file uploads
    $file_fields = [
        'cv_file' => 'cvFile',
        'passport_file' => 'passportFile',
        'id_file' => 'idFile',
        'bachelor_certificate_file' => 'bachelorCertificate',
        'masters_certificate_file' => 'mastersCertificate',
        'experience_certificate_file' => 'experienceCertificate',
        'photo_file' => 'photoFile',
        'high_school_certificate_file' => 'highSchoolCertificate'
    ];
    
    foreach ($file_fields as $db_field => $form_field) {
        if (isset($_FILES[$form_field]) && $_FILES[$form_field]['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploaded_file = handleFileUpload($_FILES[$form_field], $form_field, $student_id);
            $data[$db_field] = $uploaded_file;
        } else {
            $data[$db_field] = null;
        }
    }
    
    // Insert into database
    $sql = "INSERT INTO registrations (
        first_name, father_name, grandfather_name, family_name, full_name,
        nationality, address, street_number, house_number,
        student_phone, work_phone, email,
        program_type, selected_program,
        cv_file, passport_file, id_file,
        bachelor_certificate_file, masters_certificate_file,
        experience_certificate_file, photo_file, high_school_certificate_file,
        additional_notes, submission_date
    ) VALUES (
        :first_name, :father_name, :grandfather_name, :family_name, :full_name,
        :nationality, :address, :street_number, :house_number,
        :student_phone, :work_phone, :email,
        :program_type, :selected_program,
        :cv_file, :passport_file, :id_file,
        :bachelor_certificate_file, :masters_certificate_file,
        :experience_certificate_file, :photo_file, :high_school_certificate_file,
        :additional_notes, NOW()
    )";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    
    $registration_id = $pdo->lastInsertId();
    
    // Send email notification (optional)
    sendEmailNotification($data);
    
    // Success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Registration submitted successfully',
        'registration_id' => $registration_id,
        'student_id' => $student_id
    ]);
    
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
