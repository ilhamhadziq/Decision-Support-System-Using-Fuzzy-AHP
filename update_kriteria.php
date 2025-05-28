<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_kriteria = $_POST['id_kriteria'];
    $nama_kriteria = $_POST['nama_kriteria'];

    // Query untuk update data
    $query = "UPDATE kriteria SET nama_kriteria = '$nama_kriteria' WHERE id_kriteria = '$id_kriteria'";
    
    if (mysqli_query($conn, $query)) {
        echo "Data berhasil diperbarui";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
