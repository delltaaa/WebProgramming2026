<?php
$page_title = 'Tambah Anggota';
$active_page = 'anggota';
$body_restrict = 'admin';
include '../includes/header.php';

$old = $_SESSION['old_anggota'] ?? [];
unset($_SESSION['old_anggota']);

$errors = $_SESSION['form_errors_anggota'] ?? [];
unset($_SESSION['form_errors_anggota']);

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
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Anggota
    </a>

    <div class="form-card">
        <h2 class="form-card-title">Tambah Anggota Baru</h2>
        <p class="form-card-desc">Isi detail informasi anggota di bawah ini.</p>

        <form action="proses_tambah.php" method="post" novalidate>
            <div class="form-grid">

                <div class="form-group<?= !empty($errors['id_anggota']) ? ' has-error' : '' ?>">
                    <label for="id_anggota">ID Anggota</label>
                    <input type="text" id="id_anggota" name="id_anggota"
                        value="<?= htmlspecialchars($old['id_anggota'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Contoh: AG-103"
                        <?= !empty($errors['id_anggota']) ? 'aria-invalid="true"' : '' ?> required>
                    <?php if (!empty($errors['id_anggota'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['id_anggota']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group<?= !empty($errors['telepon']) ? ' has-error' : '' ?>">
                    <label for="telepon">Nomor Telepon / WA</label>
                    <input type="tel" id="telepon" name="telepon"
                        value="<?= htmlspecialchars($old['telepon'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="08xxxxxxxxxx"
                        <?= !empty($errors['telepon']) ? 'aria-invalid="true"' : '' ?> required>
                    <?php if (!empty($errors['telepon'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['telepon']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group full-width<?= !empty($errors['nama']) ? ' has-error' : '' ?>">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama"
                        value="<?= htmlspecialchars($old['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Masukkan nama lengkap anggota"
                        <?= !empty($errors['nama']) ? 'aria-invalid="true"' : '' ?> required>
                    <?php if (!empty($errors['nama'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['nama']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group full-width<?= !empty($errors['email']) ? ' has-error' : '' ?>">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" name="email"
                        value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="contoh@domain.com"
                        <?= !empty($errors['email']) ? 'aria-invalid="true"' : '' ?> required>
                    <?php if (!empty($errors['email'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['email']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group full-width<?= !empty($errors['alamat']) ? ' has-error' : '' ?>">
                    <label for="alamat">Alamat Tinggal</label>
                    <textarea id="alamat" name="alamat" rows="2" placeholder="Alamat lengkap..."><?= htmlspecialchars($old['alamat'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    <?php if (!empty($errors['alamat'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['alamat']) ?></small>
                    <?php endif; ?>
                </div>

            </div>

            <div class="form-actions">
                <a href="list.php" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-submit"><i class="fa-solid fa-user-check"></i> Simpan Anggota</button>
            </div>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
