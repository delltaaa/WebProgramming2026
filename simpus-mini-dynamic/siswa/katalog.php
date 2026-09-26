<?php
$page_title = 'Katalog Buku';
$active_page = 'katalog';
$body_restrict = 'anggota';
include '../includes/header.php';
require_once '../includes/koneksi.php';
require_once '../includes/buku_gambar.php';
ensureBukuImageColumn($pdo);

$keyword = trim($_GET['q'] ?? '');
$kategori = trim($_GET['kategori'] ?? '');

$categories = $pdo->query("SELECT DISTINCT kategori FROM buku WHERE kategori IS NOT NULL AND kategori <> '' ORDER BY kategori ASC")->fetchAll(PDO::FETCH_COLUMN);

$sql = 'SELECT * FROM buku WHERE 1=1';
$params = [];

if ($keyword !== '') {
    $sql .= ' AND (judul ILIKE :keyword OR pengarang ILIKE :keyword OR kode_buku ILIKE :keyword OR isbn ILIKE :keyword)';
    $params['keyword'] = '%' . $keyword . '%';
}

if ($kategori !== '') {
    $sql .= ' AND kategori = :kategori';
    $params['kategori'] = $kategori;
}

$sql .= ' ORDER BY id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$buku_list = $stmt->fetchAll();
?>

<main class="container catalog-page">
    <div class="page-header">
        <div class="page-title">
            <h2><i class="fa-solid fa-book-open-reader"></i> Katalog Buku</h2>
            <p>Temukan buku yang tersedia di perpustakaan dan lihat informasi lengkapnya.</p>
        </div>
    </div>

    <form class="catalog-filter" method="get" action="katalog.php">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Cari judul, pengarang, kode, atau ISBN...">
        </div>
        <select name="kategori" class="catalog-category">
            <option value="">Semua kategori</option>
            <?php foreach ($categories as $item): ?>
                <option value="<?= htmlspecialchars($item) ?>" <?= $kategori === $item ? 'selected' : '' ?>><?= htmlspecialchars($item) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-primary"><i class="fa-solid fa-filter"></i> Cari</button>
        <?php if ($keyword !== '' || $kategori !== ''): ?>
            <a href="katalog.php" class="btn-secondary">Reset</a>
        <?php endif; ?>
    </form>

    <div class="catalog-result-info">
        <span><strong><?= count($buku_list) ?></strong> buku ditemukan</span>
        <?php if ($keyword !== ''): ?><span>Pencarian: <strong><?= htmlspecialchars($keyword) ?></strong></span><?php endif; ?>
        <?php if ($kategori !== ''): ?><span>Kategori: <strong><?= htmlspecialchars($kategori) ?></strong></span><?php endif; ?>
    </div>

    <?php if (empty($buku_list)): ?>
        <div class="catalog-empty">
            <i class="fa-solid fa-book-open"></i>
            <h3>Buku tidak ditemukan</h3>
            <p>Coba gunakan kata kunci atau kategori lain.</p>
        </div>
    <?php else: ?>
        <div class="catalog-grid">
            <?php foreach ($buku_list as $buku): ?>
                <article class="book-card">
                    <?php if (!empty($buku['image_path'])): ?>
                        <div class="book-cover-placeholder has-image">
                            <img src="../<?= htmlspecialchars($buku['image_path']) ?>" alt="Sampul <?= htmlspecialchars($buku['judul']) ?>" loading="lazy">
                        </div>
                    <?php else: ?>
                        <div class="book-cover-placeholder">
                            <i class="fa-solid fa-book"></i>
                            <span><?= htmlspecialchars(mb_strtoupper(mb_substr($buku['judul'], 0, 1))) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="book-card-body">
                        <div class="book-card-meta">
                            <span class="book-category"><?= htmlspecialchars($buku['kategori']) ?></span>
                            <?php if ((int)$buku['jumlah'] > 0): ?>
                                <span class="badge-status badge-available">Tersedia</span>
                            <?php else: ?>
                                <span class="badge-status badge-borrowed">Stok habis</span>
                            <?php endif; ?>
                        </div>
                        <h3><?= htmlspecialchars($buku['judul']) ?></h3>
                        <p class="book-author"><i class="fa-regular fa-user"></i> <?= htmlspecialchars($buku['pengarang']) ?></p>
                        <p class="book-stock"><i class="fa-solid fa-layer-group"></i> <?= (int)$buku['jumlah'] ?> eksemplar</p>
                        <a href="detail.php?id=<?= (int)$buku['id'] ?>" class="btn-detail">Lihat Detail <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
