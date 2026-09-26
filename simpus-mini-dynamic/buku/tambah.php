<?php
$page_title = 'Tambah Buku';
$active_page = 'buku';
$body_restrict = 'admin';
include '../includes/header.php';

$old = $_SESSION['old_buku'] ?? [];
unset($_SESSION['old_buku']);

$errors = $_SESSION['form_errors_buku'] ?? [];
unset($_SESSION['form_errors_buku']);

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<?php if ($flash): ?>
<div id="toast-container" aria-live="polite">
    <div class="toast toast-<?= htmlspecialchars($flash['type']) ?>">
        <div class="toast-icon">
            <i class="fa-solid fa-circle-exclamation"></i>
        </div>
        <div class="toast-msg"><?= htmlspecialchars($flash['message']) ?></div>
    </div>
</div>
<?php endif; ?>

<main class="container">
    <a href="list.php" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Buku
    </a>

    <div class="form-card">
        <h2 class="form-card-title">Tambah Buku Baru</h2>
        <p class="form-card-desc">Isi detail informasi buku di bawah ini.</p>

        <form action="proses_tambah.php" method="post" enctype="multipart/form-data" novalidate>
            <div class="form-grid">

                <div class="form-group<?= !empty($errors['kode_buku']) ? ' has-error' : '' ?>">
                    <label for="kode_buku">Kode Buku</label>
                    <input type="text" id="kode_buku" name="kode_buku"
                        value="<?= htmlspecialchars($old['kode_buku'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Contoh: BK-003"
                        <?= !empty($errors['kode_buku']) ? 'aria-invalid="true"' : '' ?> required>
                    <?php if (!empty($errors['kode_buku'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['kode_buku']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group<?= !empty($errors['isbn']) ? ' has-error' : '' ?>">
                    <label for="isbn">Nomor ISBN</label>
                    <input type="text" id="isbn" name="isbn"
                        value="<?= htmlspecialchars($old['isbn'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="978-xxx-xxxx-xx-x">
                    <?php if (!empty($errors['isbn'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['isbn']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group full-width<?= !empty($errors['judul']) ? ' has-error' : '' ?>">
                    <label for="judul">Judul Buku</label>
                    <input type="text" id="judul" name="judul"
                        value="<?= htmlspecialchars($old['judul'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Masukkan judul buku lengkap"
                        <?= !empty($errors['judul']) ? 'aria-invalid="true"' : '' ?> required>
                    <?php if (!empty($errors['judul'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['judul']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group<?= !empty($errors['pengarang']) ? ' has-error' : '' ?>">
                    <label for="pengarang">Pengarang / Penulis</label>
                    <input type="text" id="pengarang" name="pengarang"
                        value="<?= htmlspecialchars($old['pengarang'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Nama penulis"
                        <?= !empty($errors['pengarang']) ? 'aria-invalid="true"' : '' ?> required>
                    <?php if (!empty($errors['pengarang'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['pengarang']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group<?= !empty($errors['kategori']) ? ' has-error' : '' ?>">
                    <label for="kategori">Kategori</label>
                    <select id="kategori" name="kategori" <?= !empty($errors['kategori']) ? 'aria-invalid="true"' : '' ?> required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach (['Teknologi','Pemrograman','Sains','Fiksi','Basis Data','Novel','Pengembangan Diri','Sejarah'] as $kategori): ?>
                            <option value="<?= htmlspecialchars($kategori) ?>" <?= ($old['kategori'] ?? '') === $kategori ? 'selected' : '' ?>><?= htmlspecialchars($kategori) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['kategori'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['kategori']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group<?= !empty($errors['jumlah']) ? ' has-error' : '' ?>">
                    <label for="jumlah">Jumlah Stok Buku</label>
                    <input type="number" id="jumlah" name="jumlah" min="1"
                        value="<?= htmlspecialchars($old['jumlah'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="1"
                        <?= !empty($errors['jumlah']) ? 'aria-invalid="true"' : '' ?> required>
                    <?php if (!empty($errors['jumlah'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['jumlah']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group<?= !empty($errors['tahun']) ? ' has-error' : '' ?>">
                    <label for="tahun">Tahun Terbit</label>
                    <input type="number" id="tahun" name="tahun" min="1000" max="<?= date('Y') ?>"
                        value="<?= htmlspecialchars($old['tahun'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="2026"
                        <?= !empty($errors['tahun']) ? 'aria-invalid="true"' : '' ?>>
                    <?php if (!empty($errors['tahun'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['tahun']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group full-width<?= !empty($errors['gambar']) ? ' has-error' : '' ?>">
                    <label for="gambar">Gambar Sampul</label>
                    <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp" <?= !empty($errors['gambar']) ? 'aria-invalid="true"' : '' ?>>
                    <small class="form-help">JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                    <?php if (!empty($errors['gambar'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['gambar']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group full-width<?= !empty($errors['deskripsi']) ? ' has-error' : '' ?>">
                    <label for="deskripsi">Deskripsi Singkat / Ringkasan</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Catatan atau sinopsis buku..."><?= htmlspecialchars($old['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    <?php if (!empty($errors['deskripsi'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['deskripsi']) ?></small>
                    <?php endif; ?>
                </div>

            </div>

            <div class="form-actions">
                <a href="list.php" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Buku</button>
            </div>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
