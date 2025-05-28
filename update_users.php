<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Password langsung diupdate
    $role = $_POST['role'];

    $query = "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $username, $password, $role, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($result) {
            echo "Data berhasil diperbarui!";
        } else {
            echo "Gagal memperbarui data: " . mysqli_error($conn);
        }
    } else {
        echo "Gagal mempersiapkan query: " . mysqli_error($conn);
    }
}
?>
