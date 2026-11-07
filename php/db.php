<?php
// php/db.php — environment-aware PDO connection

if (file_exists(__DIR__ . '/db_config.php')) {
    require_once __DIR__ . '/db_config.php';
}

$host = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: '127.0.0.1');
$port = defined('DB_PORT') ? DB_PORT : (getenv('DB_PORT') ?: '3306');
$db   = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'sigfrid_portfolio');
$user = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'root');
$pass = defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') ?: '');
$sslCa = getenv('DB_SSL_CA') ?: '';
$sslVerify = filter_var(getenv('DB_SSL_VERIFY') ?: 'true', FILTER_VALIDATE_BOOLEAN);

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

if ($sslCa !== '') {
    $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $sslVerify;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log('DB connection failed: ' . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'database connection failed']);
    exit(0);
}