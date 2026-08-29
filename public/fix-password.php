<?php
/**
 * Direct Database Password Repair Utility
 *
 * This script runs independently of Laravel to fix the "This password does not use the Bcrypt algorithm" issue.
 * It reads the database credentials directly from the .env file, connects via PDO,
 * and updates the admin's password to a fresh Bcrypt hash.
 */

header('Content-Type: text/plain');

$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    die("Error: .env file not found at: {$envPath}\n");
}

$envContent = file_get_contents($envPath);

// Helper function to extract env values
function getEnvValue($key, $content) {
    if (preg_match('/^' . preg_quote($key, '/') . '=(.*)$/m', $content, $matches)) {
        return trim($matches[1], "\"' \r\n");
    }
    return '';
}

$dbHost = getEnvValue('DB_HOST', $envContent) ?: '127.0.0.1';
$dbName = getEnvValue('DB_DATABASE', $envContent);
$dbUser = getEnvValue('DB_USERNAME', $envContent);
$dbPass = getEnvValue('DB_PASSWORD', $envContent);

if (empty($dbName) || empty($dbUser)) {
    die("Error: Database name or username is empty in .env.\n");
}

echo "Database: {$dbName}\n";
echo "Host: {$dbHost}\n";
echo "User: {$dbUser}\n";
echo "Connecting...\n";

try {
    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    echo "Connected successfully!\n\n";

    // 1. Check if admin exists
    $stmt = $pdo->prepare("SELECT id, email, password FROM users WHERE email = 'admin@test.com'");
    $stmt->execute();
    $admin = $stmt->fetch();

    if (!$admin) {
        // If admin doesn't exist, seed them
        echo "Admin user not found. Creating admin...\n";
        $passwordHash = password_hash('password', PASSWORD_BCRYPT);
        $insert = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role, status, created_at, updated_at) VALUES ('Umar', 'Maher', 'admin@test.com', ?, 'admin', 'Approved', NOW(), NOW())");
        $insert->execute([$passwordHash]);
        echo "✅ Admin user created successfully with password: password\n";
    } else {
        // If admin exists, force update the hash
        echo "Current Hash: " . $admin['password'] . "\n";
        echo "Updating to secure Bcrypt hash...\n";
        
        $passwordHash = password_hash('password', PASSWORD_BCRYPT);
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$passwordHash, $admin['id']]);
        
        echo "✅ Admin password hash updated successfully!\n";
    }

} catch (\PDOException $e) {
    die("Database Error: " . $e->getMessage() . "\n");
}
