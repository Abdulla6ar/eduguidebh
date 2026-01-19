<?php
declare(strict_types=1);

function get_db_path(): string {
    $data_dir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
    if (!is_dir($data_dir)) {
        mkdir($data_dir, 0755, true);
    }

    return $data_dir . DIRECTORY_SEPARATOR . 'registrations.sqlite';
}

function get_db(): PDO {
    $db_path = get_db_path();
    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON;');

    return $pdo;
}

function apply_migrations(PDO $pdo): void {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS schema_migrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            filename TEXT NOT NULL UNIQUE,
            applied_at TEXT NOT NULL DEFAULT (datetime('now'))
        );"
    );

    $migrations_dir = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';
    if (!is_dir($migrations_dir)) {
        return;
    }

    $files = glob($migrations_dir . DIRECTORY_SEPARATOR . '*.sql') ?: [];
    sort($files, SORT_STRING);

    $applied = $pdo->query("SELECT filename FROM schema_migrations")->fetchAll(PDO::FETCH_COLUMN);
    $applied_set = array_flip($applied ?: []);

    foreach ($files as $file) {
        $filename = basename($file);
        if (isset($applied_set[$filename])) {
            continue;
        }

        $sql = file_get_contents($file);
        if ($sql === false) {
            throw new RuntimeException("Unable to read migration: {$filename}");
        }

        $pdo->beginTransaction();
        try {
            $pdo->exec($sql);
            $stmt = $pdo->prepare("INSERT INTO schema_migrations (filename) VALUES (:filename)");
            $stmt->execute([':filename' => $filename]);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
