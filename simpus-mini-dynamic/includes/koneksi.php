<?php
$host     = getenv('PGHOST') ?: getenv('DB_HOST') ?: 'localhost';
$port     = getenv('PGPORT') ?: getenv('DB_PORT') ?: '5432';
$dbname   = getenv('PGDATABASE') ?: getenv('DB_NAME') ?: 'railway';
$user     = getenv('PGUSER') ?: getenv('DB_USER') ?: 'postgres';
$password = getenv('PGPASSWORD') ?: getenv('DB_PASSWORD') ?: '';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Koneksi database gagal. Periksa konfigurasi PostgreSQL di includes/koneksi.php. Error: " . $e->getMessage());
}
?>
