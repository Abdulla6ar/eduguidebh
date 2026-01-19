<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        throw new RuntimeException('Invalid JSON payload');
    }

    $required = [
        'firstName',
        'fatherName',
        'grandfatherName',
        'familyName',
        'nationality',
        'address',
        'streetNumber',
        'houseNumber',
        'studentPhone',
        'email',
        'programType',
        'selectedProgram'
    ];

    $missing = [];
    foreach ($required as $field) {
        if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
            $missing[] = $field;
        }
    }

    if ($missing) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields',
            'missing' => $missing
        ]);
        exit;
    }

    $pdo = get_db();
    apply_migrations($pdo);

    $full_name = $data['fullName'] ?? trim(
        $data['firstName'] . ' ' .
        $data['fatherName'] . ' ' .
        $data['grandfatherName'] . ' ' .
        $data['familyName']
    );

    $files_json = isset($data['files']) ? json_encode($data['files'], JSON_UNESCAPED_SLASHES) : null;

    $stmt = $pdo->prepare(
        "INSERT INTO registrations (
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
            notes,
            submission_date,
            timestamp,
            files_json
        ) VALUES (
            :first_name,
            :father_name,
            :grandfather_name,
            :family_name,
            :full_name,
            :nationality,
            :address,
            :street_number,
            :house_number,
            :student_phone,
            :work_phone,
            :email,
            :program_type,
            :selected_program,
            :notes,
            :submission_date,
            :timestamp,
            :files_json
        )"
    );

    $stmt->execute([
        ':first_name' => $data['firstName'],
        ':father_name' => $data['fatherName'],
        ':grandfather_name' => $data['grandfatherName'],
        ':family_name' => $data['familyName'],
        ':full_name' => $full_name,
        ':nationality' => $data['nationality'],
        ':address' => $data['address'],
        ':street_number' => $data['streetNumber'],
        ':house_number' => $data['houseNumber'],
        ':student_phone' => $data['studentPhone'],
        ':work_phone' => $data['workPhone'] ?? null,
        ':email' => $data['email'],
        ':program_type' => $data['programType'],
        ':selected_program' => $data['selectedProgram'],
        ':notes' => $data['notes'] ?? null,
        ':submission_date' => $data['submissionDate'] ?? null,
        ':timestamp' => $data['timestamp'] ?? null,
        ':files_json' => $files_json
    ]);

    echo json_encode([
        'success' => true,
        'id' => $pdo->lastInsertId(),
        'message' => 'Registration saved'
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
