<?php
/**
 * Test Database Connection
 * Upload this to /var/www/html/api/
 * Visit: http://164.90.228.133/api/test-connection.php
 */

// IMPORTANT: Update these credentials
$db_config = [
    'host' => 'localhost',
    'database' => 'eduguidebh',
    'username' => 'YOUR_DB_USERNAME',  // ← Change this
    'password' => 'YOUR_DB_PASSWORD',  // ← Change this
    'charset' => 'utf8mb4'
];

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f5f5f5; }
        .box { background: white; padding: 30px; border-radius: 10px; max-width: 700px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; font-size: 24px; }
        .error { color: #dc3545; font-weight: bold; font-size: 24px; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        h1 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        .step { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 10px 0; }
    </style>
</head>
<body>
    <div class='box'>
        <h1>🔌 Database Connection Test</h1>
        
        <div class='info'>
            <strong>Configuration:</strong><br>
            Host: <?php echo htmlspecialchars($db_config['host']); ?><br>
            Database: <?php echo htmlspecialchars($db_config['database']); ?><br>
            Username: <?php echo htmlspecialchars($db_config['username']); ?><br>
            Password: <?php echo ($db_config['password'] && $db_config['password'] !== 'YOUR_DB_PASSWORD') ? '✓ Set' : '❌ NOT SET'; ?>
        </div>

<?php
if ($db_config['password'] === 'YOUR_DB_PASSWORD' || empty($db_config['password'])) {
    echo "<div class='error'>❌ Database credentials not configured!</div>";
    echo "<div class='step'><strong>Action Required:</strong><br>";
    echo "1. Edit this file: <code>/var/www/html/api/test-connection.php</code><br>";
    echo "2. Update the username and password<br>";
    echo "3. Refresh this page</div>";
    echo "</div></body></html>";
    exit;
}

try {
    $dsn = "mysql:host={$db_config['host']};dbname={$db_config['database']};charset={$db_config['charset']}";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<h2 class='success'>✅ Connection Successful!</h2>";
    echo "<p>Successfully connected to the database!</p>";
    
    // Get MySQL version
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "<p><strong>MySQL Version:</strong> $version</p>";
    
    // Check if registrations table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'registrations'");
    if ($stmt->rowCount() > 0) {
        echo "<p class='success'>✅ 'registrations' table exists</p>";
        
        // Get table structure
        $columns = $pdo->query("DESCRIBE registrations")->fetchAll();
        echo "<h3>Table Structure:</h3>";
        echo "<pre>";
        printf("%-30s %-20s %-10s\n", "Field", "Type", "Null");
        echo str_repeat("-", 70) . "\n";
        foreach ($columns as $col) {
            printf("%-30s %-20s %-10s\n", 
                $col['Field'], 
                $col['Type'], 
                $col['Null']
            );
        }
        echo "</pre>";
        
        // Get record count
        $count = $pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
        echo "<p><strong>Total Registrations:</strong> $count</p>";
        
        if ($count > 0) {
            // Show sample records
            $recent = $pdo->query("SELECT id, full_name, email, submission_date FROM registrations ORDER BY id DESC LIMIT 5")->fetchAll();
            echo "<h3>Recent Registrations:</h3>";
            echo "<pre>";
            foreach ($recent as $rec) {
                echo "ID: {$rec['id']} | {$rec['full_name']} | {$rec['email']} | {$rec['submission_date']}\n";
            }
            echo "</pre>";
        }
        
    } else {
        echo "<p class='error'>⚠️ 'registrations' table not found!</p>";
        echo "<div class='step'>";
        echo "<strong>Action Required:</strong><br>";
        echo "The database exists but the 'registrations' table is missing.<br>";
        echo "You may need to run the database schema creation script.";
        echo "</div>";
    }
    
    // Check file upload directory
    $upload_dir = '/var/www/html/uploads/';
    if (is_dir($upload_dir)) {
        if (is_writable($upload_dir)) {
            echo "<p class='success'>✅ Upload directory exists and is writable</p>";
        } else {
            echo "<p class='error'>⚠️ Upload directory exists but is NOT writable</p>";
            echo "<div class='step'>Run: <code>chmod 775 /var/www/html/uploads</code></div>";
        }
    } else {
        echo "<p class='error'>⚠️ Upload directory does not exist</p>";
        echo "<div class='step'>Run: <code>mkdir -p /var/www/html/uploads && chmod 775 /var/www/html/uploads</code></div>";
    }
    
    echo "<hr>";
    echo "<h3>✅ System Ready!</h3>";
    echo "<p>Your database is configured correctly. You can now:</p>";
    echo "<ul>";
    echo "<li><a href='/registration-form.html'>Test Registration Form</a></li>";
    echo "<li><a href='/dashboard.html'>View Admin Dashboard</a></li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<h2 class='error'>❌ Connection Failed!</h2>";
    echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
    
    echo "<div class='step'>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ol>";
    echo "<li>Verify database credentials are correct</li>";
    echo "<li>Check if MySQL is running: <code>systemctl status mysql</code></li>";
    echo "<li>Verify database exists: <code>mysql -u root -p -e 'SHOW DATABASES;'</code></li>";
    echo "<li>Check user privileges: <code>SHOW GRANTS FOR '{$db_config['username']}'@'localhost';</code></li>";
    echo "</ol>";
    echo "</div>";
}
?>

    </div>
</body>
</html>
