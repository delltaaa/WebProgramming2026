<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/buku_gambar.php';

ensureBukuImageColumn($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id        = (int)($_POST['id'] ?? 0);
$kode      = trim($_POST['kode_buku'] ?? '');
$isbn      = trim($_POST['isbn'] ?? '');
$judul     = trim($_POST['judul'] ?? '');
$pengarang = trim($_POST['pengarang'] ?? '');
$kategori  = trim($_POST['kategori'] ?? '');
$jumlah    = trim($_POST['jumlah'] ?? '');
$tahun     = trim($_POST['tahun'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');

$errors = [];
$currentImage = '';

if ($id < 1) {
    $errors['id'] = 'ID buku tidak valid.';
}

if ($id > 0) {
    $getCurrent = $pdo->prepare('SELECT image_path FROM buku WHERE id = :id');
    $getCurrent->execute([':id' => $id]);
    $currentImage = (string)($getCurrent->fetchColumn() ?? '');
}

if ($kode === '') {
    $errors['kode_buku'] = 'Kode buku wajib diisi.';
}
if ($judul === '') {
    $errors['judul'] = 'Judul buku wajib diisi.';
}
if ($pengarang === '') {
    $errors['pengarang'] = 'Pengarang wajib diisi.';
}
if ($kategori === '') {
    $errors['kategori'] = 'Kategori wajib dipilih.';
}
if ($jumlah === '' || filter_var($jumlah, FILTER_VALIDATE_INT) === false || (int)$jumlah < 1) {
    $errors['jumlah'] = 'Jumlah stok harus berupa angka minimal 1.';
}
if ($tahun !== '' && (!ctype_digit($tahun) || (int)$tahun < 1000 || (int)$tahun > (int)date('Y'))) {
    $errors['tahun'] = 'Tahun terbit tidak valid.';
}

$gambar     = $_FILES['gambar'] ?? [];
$gambarPath = $currentImage;

if (($gambar['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    $newImage = uploadBukuGambar($gambar, $currentImage);
    if ($newImage['error'] !== '') {
        $errors['gambar'] = $newImage['error'];
    } else {
        $gambarPath = $newImage['path'];
    }
}

if (!$errors) {
    $check = $pdo->prepare('SELECT 1 FROM buku WHERE LOWER(kode_buku) = LOWER(:kode) AND id <> :id LIMIT 1');
    $check->execute([':kode' => $kode, ':id' => $id]);

    if ($check->fetchColumn()) {
        $errors['kode_buku'] = 'Kode buku sudah digunakan.';
    }
}

if ($errors) {
    if (isset($newImage) && $newImage['path'] !== $currentImage && $newImage['path'] !== '') {
        deleteBukuGambar($newImage['path']);
    }

    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Periksa kembali data pada form.'
    ];
    $_SESSION['form_errors_edit_buku'] = $errors;
    $_SESSION['old_edit_buku']          = $_POST;

    header('Location: edit.php?id=' . $id);
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE buku SET 
        kode_buku  = :kode,
        isbn       = :isbn,
        judul      = :judul,
        pengarang  = :pengarang,
        kategori   = :kategori,
        jumlah     = :jumlah,
        tahun      = :tahun,
        deskripsi  = :deskripsi,
        image_path = :image_path 
        WHERE id   = :id');

    $stmt->execute([
        ':kode'       => $kode,
        ':isbn'       => $isbn !== '' ? $isbn : null,
        ':judul'      => $judul,
        ':pengarang'  => $pengarang,
        ':kategori'   => $kategori,
        ':jumlah'     => (int)$jumlah,
        ':tahun'      => $tahun !== '' ? (int)$tahun : null,
        ':deskripsi'  => $deskripsi !== '' ? $deskripsi : null,
        ':image_path' => $gambarPath !== '' ? $gambarPath : null,
        ':id'         => $id
    ]);

    if ($gambarPath !== $currentImage && $gambarPath !== '') {
        deleteBukuGambar($currentImage);
    }

    $_SESSION['flash'] = [
        'type'    => 'success',
        'message' => 'Data buku berhasil diperbarui.'
    ];
} catch (PDOException $e) {
    if ($gambarPath !== $currentImage && $gambarPath !== '') {
        deleteBukuGambar($gambarPath);
    }

    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Data buku gagal diperbarui.'
    ];
}

header('Location: list.php');
exit;
