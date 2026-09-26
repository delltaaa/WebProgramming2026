<?php
$page_title = 'Edit Buku';
$active_page = 'buku';
$body_restrict = 'admin';
include '../includes/header.php';
require_once '../includes/koneksi.php';
require_once '../includes/buku_gambar.php';
ensureBukuImageColumn($pdo);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'ID buku tidak valid.'];
    header('Location: list.php'); exit;
}

$stmt = $pdo->prepare('SELECT * FROM buku WHERE id = :id');
$stmt->execute([':id' => $id]);
$buku = $stmt->fetch();
if (!$buku) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Data buku tidak ditemukan.'];
    header('Location: list.php'); exit;
}

$old = $_SESSION['old_edit_buku'] ?? $buku;
$errors = $_SESSION['form_errors_edit_buku'] ?? [];
unset($_SESSION['old_edit_buku'], $_SESSION['form_errors_edit_buku']);

function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
$kategoriList = ['Teknologi','Pemrograman','Sains','Fiksi','Basis Data','Novel','Pengembangan Diri','Sejarah'];
?>
<main class="container">
    <a href="list.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Buku</a>
    <div class="form-card">
        <h2 class="form-card-title">Edit Data Buku</h2>
        <p class="form-card-desc">Perbarui informasi buku di bawah ini.</p>
        <form action="proses_edit.php" method="post" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="id" value="<?= (int)$id ?>">
            <div class="form-grid">
                <?php
                $fields = [
                    ['kode_buku','Kode Buku','Contoh: BK-003','text',false],
                    ['isbn','Nomor ISBN','978-xxx-xxxx-xx-x','text',false],
                    ['judul','Judul Buku','Masukkan judul buku lengkap','text',true],
                    ['pengarang','Pengarang / Penulis','Nama penulis','text',false],
                ];
                foreach ($fields as [$name,$label,$placeholder,$type,$full]): $err=$errors[$name]??''; ?>
                    <div class="form-group<?= $err ? ' has-error':'' ?><?= $full ? ' full-width':'' ?>">
                        <label for="<?= $name ?>"><?= $label ?></label>
                        <input type="<?= $type ?>" id="<?= $name ?>" name="<?= $name ?>" value="<?= e($old[$name]??'') ?>" placeholder="<?= e($placeholder) ?>"<?= $err?' aria-invalid="true"':'' ?><?= in_array($name,['kode_buku','judul','pengarang'])?' required':'' ?>>
                        <?php if($err): ?><small class="form-error"><?= e($err) ?></small><?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php $err=$errors['kategori']??''; ?>
                <div class="form-group<?= $err?' has-error':'' ?>">
                    <label for="kategori">Kategori</label>
                    <select id="kategori" name="kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach($kategoriList as $k): ?><option value="<?=e($k)?>" <?=($old['kategori']??'')===$k?'selected':''?>><?=e($k)?></option><?php endforeach; ?>
                    </select>
                    <?php if($err): ?><small class="form-error"><?=e($err)?></small><?php endif; ?>
                </div>

                <?php foreach ([['jumlah','Jumlah Stok Buku','1','number',true],['tahun','Tahun Terbit','2026','number',false]] as [$name,$label,$placeholder,$type,$required]): $err=$errors[$name]??''; ?>
                    <div class="form-group<?= $err?' has-error':'' ?>">
                        <label for="<?= $name ?>"><?= $label ?></label>
                        <input type="<?= $type ?>" id="<?= $name ?>" name="<?= $name ?>" value="<?= e($old[$name]??'') ?>" placeholder="<?=e($placeholder)?>"<?= $name==='jumlah'?' min="1"':'' ?><?= $name==='tahun'?' min="1000" max="'.date('Y').'"':'' ?><?= $required?' required':'' ?>>
                        <?php if($err): ?><small class="form-error"><?=e($err)?></small><?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php $err=$errors['gambar']??''; ?>
                <div class="form-group full-width<?= $err?' has-error':'' ?>">
                    <label for="gambar">Gambar Sampul</label>
                    <?php if (!empty($buku['image_path'])): ?>
                        <div class="current-book-image"><img src="../<?=e($buku['image_path'])?>" alt="Sampul <?=e($buku['judul'])?>"></div>
                    <?php endif; ?>
                    <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp"<?= $err?' aria-invalid="true"':'' ?>>
                    <small class="form-help"><?= !empty($buku['image_path']) ? 'Pilih gambar baru jika ingin mengganti sampul. ' : '' ?>JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                    <?php if($err): ?><small class="form-error"><?=e($err)?></small><?php endif; ?>
                </div>

                <?php $err=$errors['deskripsi']??''; ?>
                <div class="form-group full-width<?= $err?' has-error':'' ?>">
                    <label for="deskripsi">Deskripsi Singkat / Ringkasan</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Catatan atau sinopsis buku..."><?=e($old['deskripsi']??'')?></textarea>
                    <?php if($err): ?><small class="form-error"><?=e($err)?></small><?php endif; ?>
                </div>
            </div>
            <div class="form-actions"><a href="list.php" class="btn-cancel">Batal</a><button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button></div>
        </form>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
