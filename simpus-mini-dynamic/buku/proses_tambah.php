<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/buku_gambar.php';
ensureBukuImageColumn($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$kode_buku = trim($_POST['kode_buku'] ?? '');
$isbn = trim($_POST['isbn'] ?? '');
$judul = trim($_POST['judul'] ?? '');
$pengarang = trim($_POST['pengarang'] ?? '');
$kategori = trim($_POST['kategori'] ?? '');
$jumlah = trim($_POST['jumlah'] ?? '');
$tahun = trim($_POST['tahun'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$gambar = $_FILES['gambar'] ?? [];
$gambarPath = '';


$errors = [];

if ($kode_buku === '') $errors['kode_buku'] = 'Kode buku wajib diisi.';
if ($judul === '') $errors['judul'] = 'Judul buku wajib diisi.';
if ($pengarang === '') $errors['pengarang'] = 'Pengarang wajib diisi.';
if ($kategori === '') $errors['kategori'] = 'Kategori wajib dipilih.';
if ($jumlah === '' || filter_var($jumlah, FILTER_VALIDATE_INT) === false || (int)$jumlah < 1) {
    $errors['jumlah'] = 'Jumlah stok harus berupa angka minimal 1.';
}
if ($tahun !== '' && (!ctype_digit($tahun) || (int)$tahun < 1000 || (int)$tahun > (int)date('Y'))) {
    $errors['tahun'] = 'Tahun terbit tidak valid.';
}
if (($gambar['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    $checkImage = uploadBukuGambar($gambar);
    if ($checkImage['error'] !== '') {
        $errors['gambar'] = $checkImage['error'];
    } else {
        $gambarPath = $checkImage['path'];
    }
}

if (!$errors) {
    $check = $pdo->prepare('SELECT 1 FROM buku WHERE LOWER(kode_buku) = LOWER(:kode_buku) LIMIT 1');
    $check->execute([':kode_buku' => $kode_buku]);

    if ($check->fetchColumn()) {
        $errors['kode_buku'] = 'Kode buku sudah digunakan.';
    }
}

if ($errors) {
    if ($gambarPath !== '') { deleteBukuGambar($gambarPath); }
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Periksa kembali data pada form.'
    ];
    $_SESSION['form_errors_buku'] = $errors;
    $_SESSION['old_buku'] = $_POST;
    header('Location: tambah.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO buku (kode_buku, isbn, judul, pengarang, kategori, jumlah, tahun, deskripsi, image_path)
         VALUES (:kode_buku, :isbn, :judul, :pengarang, :kategori, :jumlah, :tahun, :deskripsi, :image_path)
         RETURNING id'
    );

    $stmt->execute([
        ':kode_buku' => $kode_buku,
        ':isbn' => $isbn !== '' ? $isbn : null,
        ':judul' => $judul,
        ':pengarang' => $pengarang,
        ':kategori' => $kategori,
        ':jumlah' => (int)$jumlah,
        ':tahun' => $tahun !== '' ? (int)$tahun : null,
        ':deskripsi' => $deskripsi !== '' ? $deskripsi : null,
        ':image_path' => $gambarPath !== '' ? $gambarPath : null,
    ]);

    $stmt->fetchColumn();

    unset($_SESSION['old_buku']);
    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Data buku berhasil ditambahkan.'
    ];
} catch (PDOException $e) {
    if ($gambarPath !== '') { deleteBukuGambar($gambarPath); }
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Data buku gagal disimpan. Pastikan data yang dimasukkan valid.'
    ];
    $_SESSION['old_buku'] = $_POST;
}

header('Location: list.php');
exit;
