<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id_kriteria = $_GET['id'];

    // Query untuk menghapus data
    $query = "DELETE FROM kriteria WHERE id_kriteria = '$id_kriteria'";
    
    if (mysqli_query($conn, $query)) {
        echo "Data berhasil dihapus";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
