<?php
$host = 'localhost';
$db_name = 'journal_db';
$username = 'root'; // Ganti dengan username database Anda
$password = '';     // Ganti dengan password database Anda

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage();
}
?>