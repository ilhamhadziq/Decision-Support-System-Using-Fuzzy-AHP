<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Password disimpan langsung
    $role = $_POST['role'];

    $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $username, $password, $role);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($result) {
            echo "Data berhasil ditambahkan!";
        } else {
            echo "Gagal menambahkan data: " . mysqli_error($conn);
        }
    } else {
        echo "Gagal mempersiapkan query: " . mysqli_error($conn);
    }
}
?>
