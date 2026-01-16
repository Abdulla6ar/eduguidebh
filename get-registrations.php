<?php
/**
 * Get Registrations API
 * Fetches all registrations from the database for admin dashboard
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php_errors.log');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
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

try {
    $pdo = connectDatabase($db_config);
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Get optional filters from query parameters
    $search = $_GET['search'] ?? null;
    $date = $_GET['date'] ?? null;
    $program = $_GET['program'] ?? null;
    
    // Build query
    $sql = "SELECT 
                id,
                submission_date,
                first_name,
                father_name,
                grandfather_name,
                family_name,
                full_name,
                nationality,
                address,
                street_number,
                house_number,
                student_phone,
                work_phone,
                email,
                program_type,
                selected_program,
                cv_file,
                passport_file,
                id_file,
                bachelor_certificate_file,
                masters_certificate_file,
                experience_certificate_file,
                photo_file,
                high_school_certificate_file,
                additional_notes,
                created_at,
                updated_at
            FROM registrations
            WHERE 1=1";
    
    $params = [];
    
    if ($search) {
        $sql .= " AND (full_name LIKE ? OR email LIKE ? OR student_phone LIKE ?)";
        $searchParam = "%{$search}%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    if ($date) {
        $sql .= " AND DATE(submission_date) = ?";
        $params[] = $date;
    }
    
    if ($program) {
        $sql .= " AND selected_program = ?";
        $params[] = $program;
    }
    
    $sql .= " ORDER BY submission_date DESC, id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $registrations = $stmt->fetchAll();
    
    // Get statistics
    $stats = [
        'total' => $pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn(),
        'today' => $pdo->query("SELECT COUNT(*) FROM registrations WHERE DATE(submission_date) = CURDATE()")->fetchColumn(),
        'week' => $pdo->query("SELECT COUNT(*) FROM registrations WHERE submission_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn(),
        'month' => $pdo->query("SELECT COUNT(*) FROM registrations WHERE submission_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn(),
    ];
    
    // Get unique programs for filter
    $programsStmt = $pdo->query("SELECT DISTINCT selected_program FROM registrations WHERE selected_program IS NOT NULL AND selected_program != '' ORDER BY selected_program");
    $programs = $programsStmt->fetchAll(PDO::FETCH_COLUMN);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $registrations,
        'stats' => $stats,
        'programs' => $programs,
        'count' => count($registrations)
    ]);
    
} catch (Exception $e) {
    error_log("Get registrations error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch registrations: ' . $e->getMessage()
    ]);
}
?>
