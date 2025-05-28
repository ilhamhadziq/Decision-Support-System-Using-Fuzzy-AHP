<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id_alternatif = $_GET['id'];

    // Query untuk menghapus data
    $query = "DELETE FROM alternatif WHERE id_alternatif = '$id_alternatif'";
    
    if (mysqli_query($conn, $query)) {
        echo "Data berhasil dihapus";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
