<?php
$page_title = 'Daftar Anggota';
$active_page = 'anggota';
$body_restrict = 'admin';
include '../includes/header.php';
require_once '../includes/koneksi.php';

$stmt = $pdo->query('SELECT * FROM anggota ORDER BY id DESC');
$anggota_list = $stmt->fetchAll();

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
            <h2>Daftar Anggota</h2>
            <p>Kelola data dan status keanggotaan perpustakaan.</p>
        </div>
        <a href="tambah.php" class="btn-add">
            <i class="fa-solid fa-user-plus"></i> Tambah Anggota Baru
        </a>
    </div>

    <div class="toolbar">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="table-search" placeholder="Cari nama anggota, ID, atau email..." data-search-table="tabel-anggota">
        </div>
    </div>

    <div class="table-card">
        <table class="data-table" id="tabel-anggota">
            <thead>
                <tr>
                    <th>ID Anggota</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($anggota_list)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data anggota.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($anggota_list as $anggota): ?>
                        <tr>
                            <td><b><?= htmlspecialchars($anggota['id_anggota']) ?></b></td>
                            <td><?= htmlspecialchars($anggota['nama']) ?></td>
                            <td><?= htmlspecialchars($anggota['email']) ?></td>
                            <td><?= htmlspecialchars($anggota['telepon']) ?></td>
                            <td>
                                <?php if (($anggota['status'] ?? 'Aktif') === 'Aktif'): ?>
                                    <span class="badge-status badge-active">Aktif</span>
                                <?php else: ?>
                                    <span class="badge-status badge-borrowed">Non-Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="edit.php?id=<?= (int)$anggota['id'] ?>" class="btn-action edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="hapus.php?id=<?= (int)$anggota['id'] ?>" class="btn-action delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus anggota ini?');"><i class="fa-solid fa-trash-can"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <p class="table-empty-state" style="display: none; padding: 1.5rem; text-align: center;">Tidak ada anggota yang cocok dengan pencarianmu.</p>

        <div class="pagination-footer">
            <div class="pagination-info" id="pagination-info">
                Menampilkan <?= count($anggota_list) ?> data
            </div>
            <div class="pagination-controls" id="pagination-controls"></div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
