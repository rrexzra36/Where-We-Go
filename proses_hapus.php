<?php
session_start();
require 'database/connection.php';

// Pastikan ada ID yang dikirim
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];
$upload_dir = 'assets/uploads/';

try {
    // Mulai transaksi untuk memastikan semua proses berhasil atau tidak sama sekali
    $conn->beginTransaction();

    // 1. Ambil nama file foto utama dari tabel 'entri'
    $stmt_main = $conn->prepare("SELECT foto_utama FROM entri WHERE id = ?");
    $stmt_main->execute([$id]);
    $entry = $stmt_main->fetch(PDO::FETCH_ASSOC);

    // 2. Ambil semua nama file foto dari tabel 'galeri' yang terkait
    $stmt_gallery = $conn->prepare("SELECT nama_file FROM galeri WHERE id_entri = ?");
    $stmt_gallery->execute([$id]);
    $gallery = $stmt_gallery->fetchAll(PDO::FETCH_ASSOC);

    // 3. Hapus file fisik dari folder 'assets/uploads/'
    // Hapus foto utama
    if ($entry && file_exists($upload_dir . $entry['foto_utama'])) {
        unlink($upload_dir . $entry['foto_utama']);
    }
    // Hapus semua foto galeri
    foreach ($gallery as $photo) {
        if (file_exists($upload_dir . $photo['nama_file'])) {
            unlink($upload_dir . $photo['nama_file']);
        }
    }

    // 4. Hapus data dari database
    // Cukup hapus dari tabel 'entri'. Data di 'galeri' akan ikut terhapus
    // karena sudah diatur 'ON DELETE CASCADE' pada Foreign Key di database.
    $stmt_delete = $conn->prepare("DELETE FROM entri WHERE id = ?");
    $stmt_delete->execute([$id]);

    // Jika semua langkah di atas berhasil, konfirmasi perubahan
    $conn->commit();

    // Kirim pesan sukses dan kembali ke halaman utama
    $_SESSION['status'] = 'success';
    $_SESSION['message'] = 'Cerita perjalanan telah dihapus.';
    header('Location: index.php');
    exit();
} catch (Exception $e) {
    // Jika ada satu langkah pun yang gagal, batalkan semua perubahan
    $conn->rollBack();
    $_SESSION['status'] = 'failed';
    $_SESSION['message'] = 'Gagal menghapus data: ' . $e->getMessage();
    // Kembali ke halaman detail jika gagal
    header('Location: entri.php?id=' . $id);
    exit();
}
