<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/buku_gambar.php';
ensureBukuImageColumn($pdo);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'ID buku tidak valid.'];
    header('Location: list.php'); exit;
}

try {
    $get = $pdo->prepare('SELECT image_path FROM buku WHERE id = :id');
    $get->execute([':id' => $id]);
    $imagePath = (string)($get->fetchColumn() ?? '');

    $stmt = $pdo->prepare('DELETE FROM buku WHERE id = :id');
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount()) {
        deleteBukuGambar($imagePath);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data buku berhasil dihapus.'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Data buku tidak ditemukan.'];
    }
} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Data buku gagal dihapus.'];
}
header('Location: list.php'); exit;
