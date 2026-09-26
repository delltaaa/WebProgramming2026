<?php
$host = 'localhost';
$port = '5432';
$dbname = 'simpus_mini';
$user = 'postgres';
$pass = 'enter code';

$dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Periksa konfigurasi PostgreSQL di includes/koneksi.php.');
}
?>
