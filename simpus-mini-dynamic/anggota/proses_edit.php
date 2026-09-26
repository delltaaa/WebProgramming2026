<?php
session_start();
require_once '../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id         = (int)($_POST['id'] ?? 0);
$id_anggota = trim($_POST['id_anggota'] ?? '');
$nama       = trim($_POST['nama'] ?? '');
$email      = trim($_POST['email'] ?? '');
$telepon    = trim($_POST['telepon'] ?? '');
$alamat     = trim($_POST['alamat'] ?? '');
$status     = trim($_POST['status'] ?? '');

$errors = [];

if ($id < 1) {
    $errors['id'] = 'ID anggota tidak valid.';
}
if ($id_anggota === '') {
    $errors['id_anggota'] = 'ID anggota wajib diisi.';
}
if ($nama === '') {
    $errors['nama'] = 'Nama lengkap wajib diisi.';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Alamat email tidak valid.';
}
if ($telepon === '') {
    $errors['telepon'] = 'Nomor telepon wajib diisi.';
}
if (!in_array($status, ['Aktif', 'Non-Aktif'], true)) {
    $errors['status'] = 'Status anggota tidak valid.';
}

if (!$errors) {
    $check = $pdo->prepare('SELECT 1 FROM anggota WHERE LOWER(id_anggota) = LOWER(:id_anggota) AND id <> :id LIMIT 1');
    $check->execute([
        ':id_anggota' => $id_anggota,
        ':id'         => $id
    ]);

    if ($check->fetchColumn()) {
        $errors['id_anggota'] = 'ID anggota sudah digunakan.';
    }
}

if ($errors) {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Periksa kembali data pada form.'
    ];
    $_SESSION['form_errors_edit_anggota'] = $errors;
    $_SESSION['old_edit_anggota']          = $_POST;

    header('Location: edit.php?id=' . $id);
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE anggota SET 
        id_anggota = :id_anggota,
        nama       = :nama,
        email      = :email,
        telepon    = :telepon,
        alamat     = :alamat,
        status     = :status 
        WHERE id   = :id');

    $stmt->execute([
        ':id_anggota' => $id_anggota,
        ':nama'       => $nama,
        ':email'      => $email,
        ':telepon'    => $telepon,
        ':alamat'     => $alamat !== '' ? $alamat : null,
        ':status'     => $status,
        ':id'         => $id
    ]);

    $_SESSION['flash'] = [
        'type'    => 'success',
        'message' => 'Data anggota berhasil diperbarui.'
    ];
} catch (PDOException $e) {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Data anggota gagal diperbarui.'
    ];
}

header('Location: list.php');
exit;
