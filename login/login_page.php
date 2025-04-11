<?php
session_start();
include '../includes/koneksi.php';

if (isset($_SESSION['user_id'])) {
  header("Location: ../dashboard/dashboard.php");
  exit();
}

header("Cache-Control: no-cache, no-store, must-revalidate"); // Mencegah cache
header("Pragma: no-cache"); // Mencegah cache di browser lama
header("Expires: 0"); // Mengatur agar halaman langsung kadaluarsa


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username= $_POST['username'];
    $password = md5($_POST['password']); // Sesuaikan dengan metode enkripsi password

    // Query untuk memeriksa username
    $query = "SELECT * FROM user WHERE (username = '$username') AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Ambil data pengguna
        $row = $result->fetch_assoc();

        // Simpan informasi pengguna ke session
        $_SESSION['username'] = $row['username'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['user_id'] = $row['id']; // Pastikan kolom 'id' ada di tabel 'user'

        // Redirect ke halaman dashboard
        echo "<script>
                alert('Login berhasil!');
                window.location.href = '../dashboard/index.php';
              </script>";
    } else {
        // Login gagal
        echo "<script>
                alert('Username dan Password tidak cocok!');
                window.location.href = 'login_page.php';
              </script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Login</title>
</head>
<body>
  <div class="wrapper">
    <form action="login_page.php" method="POST">
        <h1>Login</h1>
        <div class="login-box">
            <input type="text" name="username" autocomplete="off" placeholder="Username" required>
            <i class='bx bx-user'></i>
        </div>
         <div class="login-box">
            <input type="password" name="password" autocomplete="new-password" placeholder="Password" required>
            <i class='bx bx-lock-alt'></i>
        </div>
        <div class="remember-me-link">
          <a href="lupa_password.php">Lupa Password?</a>
        </div>
        <button type="submit" class="btn">Login</button>
        <div class="register-link">
             <p>Tidak mempunyai akun? <a href="daftar_page.php">DAFTAR</a></p>
        </div>
</form>
  </div>
</body>
</html>
