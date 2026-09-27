<?php
$host   = 'ep-quiet-paper-b3adagai-pooler.c-4.ap-southeast-1.aws.neon.tech';
$port   = '5432';
$dbname = 'neondb';
$user   = 'neondb_owner';
$pass   = 'npg_noDrS36tmdzY';

$dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require;options='--search_path=public'";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Periksa konfigurasi PostgreSQL di includes/koneksi.php.');
}
?>
