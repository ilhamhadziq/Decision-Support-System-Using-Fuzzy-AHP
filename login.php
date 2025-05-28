<?php
session_start();
include 'config.php'; // Ganti dengan konfigurasi koneksi database Anda

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['login_user'] = $row['username'];
        $_SESSION['role'] = $row['role']; // Simpan role ke session
        header("location: index.php");
    }else {
        $error = "Username atau Password salah!";
    }
    
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>

    <!-- Custom fonts and styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        body, html {
            height: 100%;
            margin: 0;
            background: url('path/to/gasek.png') no-repeat center center fixed;
            background-size: cover;
        }

        .vh-100 {
            height: 100vh;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        }

        .p-5 {
            padding: 40px !important;
        }

        .welcome-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-right: 15px;
        }

        .form-control-user {
            text-align: center;
            padding: 15px;
            font-size: 16px;
        }

        .btn-user {
            font-size: 16px;
            padding: 12px;
            width: 100%;
        }

        .alert {
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container-fluid d-flex justify-content-center align-items-center vh-100">
        <div class="col-lg-4 col-md-6 col-sm-8">
            <div class="card o-hidden border-0 shadow-lg">
                <div class="card-body p-5">
                    <!-- Header -->
                    <div class="text-center d-flex align-items-center justify-content-center mb-4">
                        <img src="path/to/logo.png" alt="Logo" class="welcome-logo">
                        <h1 class="h4 text-gray-900">Silakan Login untuk Mengakses Penentuan Kelas</h1>
                    </div>

                    <!-- Error Message -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form class="user" method="post" action="">
                        <div class="form-group">
                            <input type="text" class="form-control form-control-user" name="username" placeholder="Masukkan Username Anda" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control form-control-user" name="password" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-user">
                            Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>
