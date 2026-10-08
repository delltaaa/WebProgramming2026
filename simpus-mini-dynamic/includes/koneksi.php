<?php

$host = getenv('PGHOST');
$port = getenv('PGPORT') ?: '5432';
$dbname = getenv('PGDATABASE');
$user = getenv('PGUSER');
$pass = getenv('PGPASSWORD');

exit(
    'HOST=' . ($host ?: 'KOSONG') . "\n" .
    'PORT=' . ($port ?: 'KOSONG') . "\n" .
    'DATABASE=' . ($dbname ?: 'KOSONG') . "\n" .
    'USER=' . ($user ?: 'KOSONG') . "\n" .
    'PASSWORD=' . ($pass !== false && $pass !== '' ? 'ADA' : 'KOSONG')
);
