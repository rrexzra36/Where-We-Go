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

    // 1. Get all image file names related to this entry
    // Get main photo
    $stmt_main = $conn->prepare("SELECT foto_utama FROM entri WHERE id = ?");
    $stmt_main->execute([$id]);
    $entry = $stmt_main->fetch(PDO::FETCH_ASSOC);

    // Get gallery photos
    $stmt_gallery = $conn->prepare("SELECT nama_file FROM galeri WHERE id_entri = ?");
    $stmt_gallery->execute([$id]);
    $gallery = $stmt_gallery->fetchAll(PDO::FETCH_ASSOC);

    // 2. Delete physical files from server
    if ($entry && file_exists($upload_dir . $entry['foto_utama'])) {
        unlink($upload_dir . $entry['foto_utama']);
    }
    foreach ($gallery as $photo) {
        if (file_exists($upload_dir . $photo['nama_file'])) {
            unlink($upload_dir . $photo['nama_file']);
        }
    }

    // 3. Delete data from database
    // Just delete from 'entri' table, data in 'galeri' will be deleted automatically
    // because 'ON DELETE CASCADE' is set on the Foreign Key.
    $stmt_delete = $conn->prepare("DELETE FROM entri WHERE id = ?");
    $stmt_delete->execute([$id]);

    $conn->commit();

    $_SESSION['status'] = 'success';
    $_SESSION['message'] = 'Travel story has been deleted.';
    header('Location: index.php');
    exit();
} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['status'] = 'failed';
    $_SESSION['message'] = 'Failed to delete data: ' . $e->getMessage();
    // Redirect back to detail page if failed
    header('Location: entri.php?id=' . $id);
    exit();
}
