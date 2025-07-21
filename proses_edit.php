<?php
session_start();
require 'database/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $tanggal_kunjungan = $_POST['tanggal_kunjungan'];
    $lokasi_nama = $_POST['lokasi_nama'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $deskripsi = $_POST['deskripsi'];
    $rating = $_POST['rating'];
    $kategori = isset($_POST['kategori']) ? $_POST['kategori'] : '';

    try {
        // 1. Update data teks di tabel entri
        $stmt = $conn->prepare("UPDATE entri SET judul = ?, tanggal_kunjungan = ?, lokasi_nama = ?, latitude = ?, longitude = ?, deskripsi = ?, rating = ?, kategori = ? WHERE id = ?");
        $stmt->execute([$judul, $tanggal_kunjungan, $lokasi_nama, $latitude, $longitude, $deskripsi, $rating, $kategori, $id]);

        // 2. Proses jika ada foto baru yang diunggah
        $upload_dir = 'assets/uploads/';
        $foto_files = $_FILES['foto'];

        if (!empty($foto_files['name'][0])) {
            $stmt_gallery = $conn->prepare("INSERT INTO galeri (id_entri, nama_file) VALUES (?, ?)");
            foreach ($foto_files['name'] as $key => $name) {
            if ($foto_files['error'][$key] === UPLOAD_ERR_OK) {
                $tmp_name = $foto_files['tmp_name'][$key];
                $file_name = time() . '_' . basename($name);
                $upload_file = $upload_dir . $file_name;

                if (move_uploaded_file($tmp_name, $upload_file)) {
                // Save new photo to the gallery table
                $stmt_gallery->execute([$id, $file_name]);
                }
            }
            }
        }

        $_SESSION['status'] = 'sukses';
        $_SESSION['pesan'] = 'Cerita perjalanan berhasil diperbarui!';
        header('Location: entri.php?id=' . $id);
        exit();
    } catch (Exception $e) {
        $_SESSION['status'] = 'gagal';
        $_SESSION['pesan'] = 'Terjadi kesalahan: ' . $e->getMessage();
        header('Location: edit.php?id=' . $id);
        exit();
    }
}
