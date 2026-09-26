<?php
function ensureBukuImageColumn(PDO $pdo): void {
    $pdo->exec("ALTER TABLE buku ADD COLUMN IF NOT EXISTS image_path VARCHAR(255)");
}

function uploadBukuGambar(array $file, string $oldPath = ''): array {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['path' => $oldPath, 'error' => ''];
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return ['path' => $oldPath, 'error' => 'Gambar gagal diunggah. Coba pilih gambar lain.'];
    }

    if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
        return ['path' => $oldPath, 'error' => 'Ukuran gambar maksimal 2 MB.'];
    }

    $tmp = $file['tmp_name'] ?? '';
    $mime = $tmp !== '' ? mime_content_type($tmp) : false;
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        return ['path' => $oldPath, 'error' => 'Format gambar harus JPG, PNG, atau WEBP.'];
    }

    $uploadDir = dirname(__DIR__) . '/assets/uploads/books/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        return ['path' => $oldPath, 'error' => 'Folder penyimpanan gambar tidak dapat dibuat.'];
    }

    $filename = 'book_' . bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    $destination = $uploadDir . $filename;

    if (!move_uploaded_file($tmp, $destination)) {
        return ['path' => $oldPath, 'error' => 'Gambar tidak dapat disimpan.'];
    }


    return ['path' => 'assets/uploads/books/' . $filename, 'error' => ''];
}

function deleteBukuGambar(string $path): void {
    if ($path === '') return;
    $file = dirname(__DIR__) . '/assets/uploads/books/' . basename($path);
    if (is_file($file)) @unlink($file);
}
?>
