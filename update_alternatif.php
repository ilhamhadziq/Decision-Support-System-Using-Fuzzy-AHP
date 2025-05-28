<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_alternatif = $_POST['id_alternatif'];
    $nama_alternatif = $_POST['nama_alternatif'];
    $kamar = $_POST['kamar'];

    $query = "UPDATE alternatif SET nama_alternatif = '$nama_alternatif', kamar = '$kamar' WHERE id_alternatif = '$id_alternatif'";

    if (mysqli_query($conn, $query)) {
        echo "Data berhasil diperbarui";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
