<?php
$page_title = 'Daftar Buku';
$active_page = 'buku';
$body_restrict = 'admin';
include '../includes/header.php';
require_once '../includes/koneksi.php';
require_once '../includes/buku_gambar.php';
ensureBukuImageColumn($pdo);

$stmt = $pdo->query('SELECT * FROM buku ORDER BY id DESC');
$buku_list = $stmt->fetchAll();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<?php if ($flash): ?>
<div id="toast-container" aria-live="polite">
    <div class="toast toast-<?= htmlspecialchars($flash['type']) ?>">
        <div class="toast-icon">
            <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        </div>
        <div class="toast-msg"><?= htmlspecialchars($flash['message']) ?></div>
    </div>
</div>
<?php endif; ?>

<main class="container">
    <div class="page-header">
        <div class="page-title">
            <h2>Daftar Buku</h2>
            <p>Kelola koleksi dan informasi buku perpustakaan.</p>
        </div>
        <a href="tambah.php" class="btn-add admin-only">
            <i class="fa-solid fa-plus"></i> Tambah Buku Baru
        </a>
    </div>

    <div class="toolbar">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="table-search" placeholder="Cari judul buku, pengarang, atau kode..." data-search-table="tabel-buku">
        </div>
    </div>

    <div class="table-card">
        <table class="data-table" id="tabel-buku">
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Kode Buku</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th class="text-center admin-only">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($buku_list)): ?>
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data buku.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($buku_list as $buku): ?>
                        <tr>
                            <td>
                                <div class="admin-book-thumb">
                                    <?php if (!empty($buku['image_path'])): ?>
                                        <img src="../<?= htmlspecialchars($buku['image_path']) ?>" alt="">
                                    <?php else: ?>
                                        <i class="fa-solid fa-book"></i>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><b><?= htmlspecialchars($buku['kode_buku']) ?></b></td>
                            <td><?= htmlspecialchars($buku['judul']) ?></td>
                            <td><?= htmlspecialchars($buku['pengarang']) ?></td>
                            <td><?= htmlspecialchars($buku['kategori']) ?></td>
                            <td><?= (int)$buku['jumlah'] ?> Eks</td>
                            <td>
                                <?php if ((int)$buku['jumlah'] > 0): ?>
                                    <span class="badge-status badge-available">Tersedia</span>
                                <?php else: ?>
                                    <span class="badge-status badge-borrowed">Dipinjam</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center admin-only">
                                <a href="edit.php?id=<?= (int)$buku['id'] ?>" class="btn-action edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="hapus.php?id=<?= (int)$buku['id'] ?>" class="btn-action delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus buku ini?');"><i class="fa-solid fa-trash-can"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <p class="table-empty-state" style="display: none; padding: 1.5rem; text-align: center;">Tidak ada buku yang cocok dengan pencarianmu.</p>

        <div class="pagination-footer">
            <div class="pagination-info" id="pagination-info">
                Menampilkan <?= count($buku_list) ?> data
            </div>
            <div class="pagination-controls" id="pagination-controls"></div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
