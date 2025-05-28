<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_alternatif = $_POST['nama_alternatif'];
    $kamar = $_POST['kamar']; // Ambil data kamar dari form

    // Dapatkan kode alternatif terakhir di database
    $query = "SELECT id_alternatif FROM alternatif ORDER BY id_alternatif DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    $lastCode = mysqli_fetch_assoc($result)['id_alternatif'];

    // Tentukan kode alternatif berikutnya
    $lastNumber = intval(substr($lastCode, 1)); // Ambil angka setelah huruf "A"
    $newCode = 'A' . ($lastNumber + 1); // Buat kode baru dengan format Axx

    // Query untuk menambahkan data baru, termasuk kolom kamar
    $query = "INSERT INTO alternatif (id_alternatif, nama_alternatif, kamar) VALUES ('$newCode', '$nama_alternatif', '$kamar')";

    if (mysqli_query($conn, $query)) {
        echo "Data berhasil ditambahkan";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
