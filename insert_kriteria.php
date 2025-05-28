<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_kriteria = $_POST['nama_kriteria'];

    // Dapatkan kode kriteria terakhir di database
    $query = "SELECT id_kriteria FROM kriteria ORDER BY id_kriteria DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    $lastCode = mysqli_fetch_assoc($result)['id_kriteria'];

    // Tentukan kode kriteria berikutnya
    $lastNumber = intval(substr($lastCode, 1)); // Ambil angka setelah huruf "K"
    $newCode = 'K' . ($lastNumber + 1); // Buat kode baru dengan format Kxx

    // Query untuk menambahkan data baru
    $query = "INSERT INTO kriteria (id_kriteria, nama_kriteria) VALUES ('$newCode', '$nama_kriteria')";
    
    if (mysqli_query($conn, $query)) {
        echo "Data berhasil ditambahkan";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
