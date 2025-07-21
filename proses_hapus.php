<?php
session_start();
require 'database/connection.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];
$upload_dir = 'assets/uploads/';

try {
    $conn->beginTransaction();

    // 1. Ambil semua nama file gambar yang terkait dengan entri ini
    // Ambil foto utama
    $stmt_main = $conn->prepare("SELECT foto_utama FROM entri WHERE id = ?");
    $stmt_main->execute([$id]);
    $entri = $stmt_main->fetch(PDO::FETCH_ASSOC);

    // Ambil foto dari galeri
    $stmt_gallery = $conn->prepare("SELECT nama_file FROM galeri WHERE id_entri = ?");
    $stmt_gallery->execute([$id]);
    $galeri = $stmt_gallery->fetchAll(PDO::FETCH_ASSOC);

    // 2. Hapus file fisik dari server
    if ($entri && file_exists($upload_dir . $entri['foto_utama'])) {
        unlink($upload_dir . $entri['foto_utama']);
    }
    foreach ($galeri as $foto) {
        if (file_exists($upload_dir . $foto['nama_file'])) {
            unlink($upload_dir . $foto['nama_file']);
        }
    }

    // 3. Hapus data dari database
    // Cukup hapus dari tabel 'entri', data di 'galeri' akan terhapus otomatis
    // karena kita sudah set 'ON DELETE CASCADE' pada Foreign Key.
    $stmt_delete = $conn->prepare("DELETE FROM entri WHERE id = ?");
    $stmt_delete->execute([$id]);

    $conn->commit();

    $_SESSION['status'] = 'sukses';
    $_SESSION['pesan'] = 'Cerita perjalanan telah dihapus.';
    header('Location: index.php');
    exit();
} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['status'] = 'gagal';
    $_SESSION['pesan'] = 'Gagal menghapus data: ' . $e->getMessage();
    // Kembali ke halaman detail jika gagal
    header('Location: entri.php?id=' . $id);
    exit();
}
