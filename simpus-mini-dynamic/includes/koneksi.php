<?php
$db_url = getenv('DATABASE_URL');

if ($db_url) {
    $dbopts = parse_url($db_url);
    $host     = $dbopts['host'];
    $port     = $dbopts['port'] ?? '5432';
    $user     = $dbopts['user'];
    $password = $dbopts['pass'];
    $dbname   = ltrim($dbopts['path'], '/');
} else {
    $host     = 'localhost';
    $port     = '5432';
    $dbname   = 'railway';
    $user     = 'postgres';
    $password = '';
}

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Koneksi database gagal. Error: " . $e->getMessage());
}
?>
