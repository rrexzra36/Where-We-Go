<?php
session_start();
require 'database/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['judul'];
    $visit_date = $_POST['tanggal_kunjungan'];
    $location_name = $_POST['lokasi_nama'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $description = $_POST['deskripsi'];
    $rating = $_POST['rating'];
    $categories = isset($_POST['kategori']) ? $_POST['kategori'] : '';

    $upload_dir = 'assets/uploads/';
    $photo_files = $_FILES['foto'];
    $uploaded_files = [];
    $main_photo = '';

    // Process file upload
    foreach ($photo_files['name'] as $key => $name) {
        if ($photo_files['error'][$key] === UPLOAD_ERR_OK) {
            $tmp_name = $photo_files['tmp_name'][$key];
            $file_name = time() . '_' . basename($name);
            $upload_file = $upload_dir . $file_name;

            if (move_uploaded_file($tmp_name, $upload_file)) {
                $uploaded_files[] = $file_name;
                if (empty($main_photo)) {
                    $main_photo = $file_name; // First file becomes the main photo
                }
            }
        }
    }

    if (empty($main_photo)) {
        // Handle error: no photo uploaded
        die("Error: Failed to upload photo. Please make sure at least one photo is uploaded.");
    }

    try {
        // Begin transaction
        $conn->beginTransaction();

        // 1. Save main entry
        $stmt = $conn->prepare("INSERT INTO entri (judul, tanggal_kunjungan, lokasi_nama, latitude, longitude, deskripsi, rating, kategori, foto_utama) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $visit_date, $location_name, $latitude, $longitude, $description, $rating, $categories, $main_photo]);

        // Get the ID of the newly inserted entry
        $entry_id = $conn->lastInsertId();

        // 2. Save gallery photos
        if (count($uploaded_files) > 1) {
            $stmt_gallery = $conn->prepare("INSERT INTO galeri (id_entri, nama_file) VALUES (?, ?)");
            // Start from the second file for the gallery
            for ($i = 1; $i < count($uploaded_files); $i++) {
                $stmt_gallery->execute([$entry_id, $uploaded_files[$i]]);
            }
        }

        // Commit transaction
        $conn->commit();

        // Set success message for SweetAlert
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Travel story saved successfully!';
        header('Location: index.php');
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'An error occurred: ' . $e->getMessage();
        header('Location: tambah.php');
        exit();
    }
}
