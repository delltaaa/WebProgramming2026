<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$projectRoot = realpath(dirname(__DIR__));
$currentDir = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
$relativeDir = str_replace('\\', '/', str_replace($projectRoot, '', $currentDir));
$relativeDir = trim($relativeDir, '/');

$depth = $relativeDir === '' ? 0 : count(array_filter(explode('/', $relativeDir)));
$base = str_repeat('../', $depth);

$page_title = $page_title ?? 'SIMPUS-Mini';
$active_page = $active_page ?? '';
$body_restrict = $body_restrict ?? 'admin';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | SIMPUS-Mini</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body data-restrict="<?= htmlspecialchars($body_restrict) ?>">
<header class="app-nav">
    <div class="logo">
        <div class="login-icon-mark"><i class="fa-solid fa-book-bookmark"></i></div>
        <h2>SIMPUS-Mini</h2>
    </div>
    <button class="nav-toggle" id="nav-toggle" aria-label="Buka menu navigasi" aria-expanded="false">
        <span></span><span></span><span></span>
    </button>
    <nav id="main-nav">
        <ul>
            <li><a href="<?= $base ?>index.php" class="<?= $active_page === 'dashboard' ? 'active' : '' ?>">Dashboard</a></li>
            <li class="admin-only"><a href="<?= $base ?>buku/list.php" class="<?= $active_page === 'buku' ? 'active' : '' ?>">Kelola Buku</a></li>
            <li class="anggota-only"><a href="<?= $base ?>siswa/katalog.php" class="<?= $active_page === 'katalog' ? 'active' : '' ?>">Katalog Buku</a></li>
            <li class="admin-only"><a href="<?= $base ?>anggota/list.php" class="<?= $active_page === 'anggota' ? 'active' : '' ?>">Kelola Anggota</a></li>
            <li><a href="#">Peminjaman</a></li>
        </ul>
    </nav>
    <div class="user-box">
        <span id="user-display"></span>
        <button type="button" class="logout-btn" data-logout>
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
        </button>
    </div>
</header>
