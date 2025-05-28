<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Jika konfirmasi logout, hapus session dan redirect ke login.php
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <script>
        function confirmLogout() {
            if (confirm("Apakah Anda yakin ingin logout?")) {
                // Mengirimkan request POST ke logout.php untuk menghapus sesi
                document.getElementById("logoutForm").submit();
            } else {
                // Jika batal, kembalikan ke halaman sebelumnya
                window.location.href = "index.php"; // Ganti dengan halaman utama Anda
            }
        }
    </script>
</head>
<body onload="confirmLogout()">
    <!-- Form untuk logout (dengan method POST untuk memproses logout) -->
    <form id="logoutForm" method="post" action="logout.php"></form>
</body>
</html>