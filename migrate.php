<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'db.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $pdo = get_db();
    apply_migrations($pdo);
    echo "Migrations applied successfully.\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "Migration failed: " . $e->getMessage() . "\n";
}
