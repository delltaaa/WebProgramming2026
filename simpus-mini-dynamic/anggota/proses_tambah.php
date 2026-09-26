<?php
session_start();
require_once '../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id_anggota = trim($_POST['id_anggota'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$telepon = trim($_POST['telepon'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');

$errors = [];

if ($id_anggota === '') $errors['id_anggota'] = 'ID anggota wajib diisi.';
if ($nama === '') $errors['nama'] = 'Nama lengkap wajib diisi.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Alamat email tidak valid.';
if ($telepon === '') $errors['telepon'] = 'Nomor telepon wajib diisi.';

if (!$errors) {
    $check = $pdo->prepare('SELECT 1 FROM anggota WHERE LOWER(id_anggota) = LOWER(:id_anggota) LIMIT 1');
    $check->execute([':id_anggota' => $id_anggota]);

    if ($check->fetchColumn()) {
        $errors['id_anggota'] = 'ID anggota sudah digunakan.';
    }
}

if ($errors) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Periksa kembali data pada form.'
    ];
    $_SESSION['form_errors_anggota'] = $errors;
    $_SESSION['old_anggota'] = $_POST;
    header('Location: tambah.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO anggota (id_anggota, nama, email, telepon, alamat, status)
         VALUES (:id_anggota, :nama, :email, :telepon, :alamat, :status)
         RETURNING id'
    );

    $stmt->execute([
        ':id_anggota' => $id_anggota,
        ':nama' => $nama,
        ':email' => $email,
        ':telepon' => $telepon,
        ':alamat' => $alamat !== '' ? $alamat : null,
        ':status' => 'Aktif',
    ]);

    $stmt->fetchColumn();

    unset($_SESSION['old_anggota']);
    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Data anggota berhasil ditambahkan.'
    ];
} catch (PDOException $e) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Data anggota gagal disimpan. Pastikan data yang dimasukkan valid.'
    ];
    $_SESSION['old_anggota'] = $_POST;
}

header('Location: list.php');
exit;
