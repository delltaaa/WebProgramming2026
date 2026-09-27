<?php
$db_url   = getenv('DATABASE_URL');
$pg_host  = getenv('PGHOST');

if ($db_url) {
    $dbopts   = parse_url($db_url);
    $host     = $dbopts['host'];
    $port     = $dbopts['port'] ?? '5432';
    $user     = $dbopts['user'];
    $password = $dbopts['pass'];
    $dbname   = ltrim($dbopts['path'], '/');
} elseif ($pg_host) {
    $host     = $pg_host;
    $port     = getenv('PGPORT') ?: '5432';
    $user     = getenv('PGUSER') ?: 'postgres';
    $password = getenv('PGPASSWORD') ?: '';
    $dbname   = getenv('PGDATABASE') ?: 'railway';
} else {
    $host     = '127.0.0.1';
    $port     = '5432';
    $user     = 'postgres';
    $password = '';
    $dbname   = 'railway';
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
