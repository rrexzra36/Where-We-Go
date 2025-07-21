<?php
session_start();
require 'database/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $tanggal_kunjungan = $_POST['tanggal_kunjungan'];
    $lokasi_nama = $_POST['lokasi_nama'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $deskripsi = $_POST['deskripsi'];
    $rating = $_POST['rating'];
    $kategori = isset($_POST['kategori']) ? implode(',', $_POST['kategori']) : '';

    $upload_dir = 'assets/uploads/';
    $foto_files = $_FILES['foto'];
    $uploaded_files = [];
    $foto_utama = '';

    // Proses upload file
    foreach ($foto_files['name'] as $key => $name) {
        if ($foto_files['error'][$key] === UPLOAD_ERR_OK) {
            $tmp_name = $foto_files['tmp_name'][$key];
            $file_name = time() . '_' . basename($name);
            $upload_file = $upload_dir . $file_name;

            if (move_uploaded_file($tmp_name, $upload_file)) {
                $uploaded_files[] = $file_name;
                if (empty($foto_utama)) {
                    $foto_utama = $file_name; // File pertama jadi foto utama
                }
            }
        }
    }

    if (empty($foto_utama)) {
        // Handle error: tidak ada foto yang diupload
        die("Error: Gagal mengunggah foto. Pastikan setidaknya satu foto diunggah.");
    }

    try {
        // Mulai transaksi
        $conn->beginTransaction();

        // 1. Simpan entri utama
        $stmt = $conn->prepare("INSERT INTO entri (judul, tanggal_kunjungan, lokasi_nama, latitude, longitude, deskripsi, rating, kategori, foto_utama) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$judul, $tanggal_kunjungan, $lokasi_nama, $latitude, $longitude, $deskripsi, $rating, $kategori, $foto_utama]);

        // Dapatkan ID dari entri yang baru saja disimpan
        $id_entri = $conn->lastInsertId();

        // 2. Simpan foto galeri
        if (count($uploaded_files) > 1) {
            $stmt_galeri = $conn->prepare("INSERT INTO galeri (id_entri, nama_file) VALUES (?, ?)");
            // Mulai dari file kedua untuk galeri
            for ($i = 1; $i < count($uploaded_files); $i++) {
                $stmt_galeri->execute([$id_entri, $uploaded_files[$i]]);
            }
        }

        // Commit transaksi
        $conn->commit();

        // Set pesan sukses untuk SweetAlert
        $_SESSION['status'] = 'sukses';
        $_SESSION['pesan'] = 'Cerita perjalanan berhasil disimpan!';
        header('Location: index.php');
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['status'] = 'gagal';
        $_SESSION['pesan'] = 'Terjadi kesalahan: ' . $e->getMessage();
        header('Location: tambah.php');
        exit();
    }
}
