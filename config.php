<?php
$host = "localhost";      // Host database
$port = "3305";           // Port database
$username = "root";       // Username database
$password = "";           // Password database
$dbname = "fahp"; // Ganti dengan nama database Anda

// Buat koneksi
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
