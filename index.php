<?php
session_start();

// Cek apakah pengguna sudah login atau belum
if (!isset($_SESSION['login_user'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Ambil role dari session
$role = $_SESSION['role'];

// Ambil halaman berdasarkan parameter 'page'
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Daftar halaman yang bisa diakses berdasarkan role
$pages_for_user = ['home', 'perangkingan'];
$pages_for_admin = ['home', 'kriteria', 'alternatif', 'bobot_kriteria','users', 'perangkingan'];

// Tentukan akses halaman berdasarkan role
if ($role == 'user' && !in_array($page, $pages_for_user)) {
    header("Location: index.php?page=home"); // Redirect ke dashboard jika halaman tidak diizinkan
    exit();
}

if ($role == 'admin' && !in_array($page, $pages_for_admin)) {
    header("Location: index.php?page=home");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Kelas Tahsin Fuzzy AHP</title>
    
    <!-- Bootstrap CSS -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            min-height: 100vh;
            background-color: #4e73df;
            color: white;
            padding-top: 20px;
            width: 300px !important;
            z-index: 1000; /* Pastikan sidebar tetap berada di atas konten lainnya */
        }
        .sidebar .nav-item {
             list-style: none; /* Menghilangkan titik pada setiap item */
        }
        .sidebar .nav-item .nav-link {
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            display: block;
        }
        .sidebar .nav-item .nav-link:hover, 
        .sidebar .nav-item .nav-link.active {
            background-color: #2e59d9;
            color: white;
            font-weight: bold;
        }
        .content-wrapper {
            margin-left: 320px !important; /* Sesuaikan margin dengan lebar sidebar */
            padding: 20px;
        }
        .sidebar-heading {
            font-size: 1.2rem;
            padding-left: 20px;
            padding-top: 10px;
        }
        .sidebar-brand-text {
            color: white !important; /* Ubah warna teks menjadi putih */
        }
    </style>
</head>

<body>

    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar d-flex flex-column p-3" id="sidebarMenu">
            <a href="index.php?page=home" class="sidebar-brand d-flex align-items-center justify-content-center mb-3">
                <div class="sidebar-brand-icon">
                    <img src="path/to/logo.png" alt="Logo" style="width:50px;">
                </div>
                <div class="sidebar-brand-text mx-3">SPK Kelas Tahsin Fuzzy AHP</div>
            </a>
            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link <?php if ($page == 'home') echo 'active'; ?>" href="index.php?page=home">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <?php if ($role == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php if ($page == 'kriteria') echo 'active'; ?>" href="index.php?page=kriteria">
                        <i class="fas fa-list-alt"></i>
                        <span>Data Kriteria</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($page == 'alternatif') echo 'active'; ?>" href="index.php?page=alternatif">
                        <i class="fas fa-list-alt"></i>
                        <span>Data Alternatif</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($page == 'bobot_kriteria') echo 'active'; ?>" href="index.php?page=bobot_kriteria">
                        <i class="fas fa-tasks"></i>
                        <span>Bobot Kriteria</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($page == 'users') echo 'active'; ?>" href="index.php?page=users">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link <?php if ($page == 'perangkingan') echo 'active'; ?>" href="index.php?page=perangkingan">
                    <i class="fas fa-chart-line"></i>
                    <span>Penentuan Kelas</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log out</span>
                </a>
            </li>
        </nav>


        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Main Content -->
            <?php
                if (file_exists($page . ".php")) {
                    include $page . ".php";
                } else {
                    echo "<h2>Page not found</h2>";
                }
            ?>
        </div>
    </div>    
</body>
</html>
