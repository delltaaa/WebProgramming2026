<?php
$page_title = 'Detail Buku';
$active_page = 'katalog';
$body_restrict = 'anggota';
include '../includes/header.php';
require_once '../includes/koneksi.php';
require_once '../includes/buku_gambar.php';
ensureBukuImageColumn($pdo);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$buku = null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM buku WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $buku = $stmt->fetch();
}
?>

<main class="container detail-page">
    <a href="katalog.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke katalog</a>

    <?php if (!$buku): ?>
        <div class="catalog-empty">
            <i class="fa-solid fa-book-open"></i>
            <h3>Buku tidak ditemukan</h3>
            <p>Data buku yang kamu cari mungkin sudah dihapus atau tidak tersedia.</p>
        </div>
    <?php else: ?>
        <div class="book-detail-card">
            <div class="book-detail-cover<?= !empty($buku['image_path']) ? ' has-image' : '' ?>">
                <?php if (!empty($buku['image_path'])): ?>
                    <img src="../<?= htmlspecialchars($buku['image_path']) ?>" alt="Sampul <?= htmlspecialchars($buku['judul']) ?>">
                <?php else: ?>
                    <i class="fa-solid fa-book"></i>
                    <span><?= htmlspecialchars(mb_strtoupper(mb_substr($buku['judul'], 0, 1))) ?></span>
                <?php endif; ?>
            </div>
            <div class="book-detail-content">
                <div class="book-card-meta">
                    <span class="book-category"><?= htmlspecialchars($buku['kategori']) ?></span>
                    <?php if ((int)$buku['jumlah'] > 0): ?>
                        <span class="badge-status badge-available">Tersedia</span>
                    <?php else: ?>
                        <span class="badge-status badge-borrowed">Stok habis</span>
                    <?php endif; ?>
                </div>
                <h1><?= htmlspecialchars($buku['judul']) ?></h1>
                <p class="detail-author">oleh <strong><?= htmlspecialchars($buku['pengarang']) ?></strong></p>

                <div class="book-detail-info">
                    <div><span>Kode Buku</span><strong><?= htmlspecialchars($buku['kode_buku']) ?></strong></div>
                    <div><span>ISBN</span><strong><?= htmlspecialchars($buku['isbn'] ?: '-') ?></strong></div>
                    <div><span>Tahun Terbit</span><strong><?= htmlspecialchars($buku['tahun'] ?: '-') ?></strong></div>
                    <div><span>Jumlah</span><strong><?= (int)$buku['jumlah'] ?> eksemplar</strong></div>
                </div>

                <div class="book-description">
                    <h3>Deskripsi</h3>
                    <p><?= nl2br(htmlspecialchars($buku['deskripsi'] ?: 'Belum ada deskripsi untuk buku ini.')) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
