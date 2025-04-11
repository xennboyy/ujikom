<?php
include '../includes/koneksi.php'; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $confirm_password = md5($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        echo "<script>
                alert('Password dan Konfirmasi Password tidak cocok!');
                window.location.href = 'daftar_page.php';
              </script>";
    } else {
        $check = "SELECT * FROM user WHERE username = '$username' OR email = '$email'";
        $result = $conn->query($check);

        if ($result->num_rows > 0) {
            echo "<script>
                    alert('Username atau email sudah terdaftar!');
                    window.location.href = 'daftar_page.php';
                  </script>";
        } else {
            $sql = "INSERT INTO user (username, email, password) VALUES ('$username', '$email', '$password')";

            if ($conn->query($sql) === TRUE) {
                echo "<script>
                        alert('Pendaftaran berhasil! Silakan login.');
                        window.location.href = 'login_page.php';
                      </script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Daftar Akun</title>
</head>
<body>
  <div class="wrapper">
    <form action="daftar_page.php" method="POST">
        <h1>Daftar Akun</h1>
        <div class="login-box">
            <input type="text" name="username" placeholder="Username" required>
            <i class='bx bx-user'></i>
        </div>
        <div class="login-box">
            <input type="email" name="email" placeholder="Email" required>
            <i class='bx bx-envelope'></i>
        </div>
        <div class="login-box">
            <input type="password" name="password" placeholder="Password" required>
            <i class='bx bx-lock-alt'></i>
        </div>
        <div class="login-box">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <i class='bx bx-lock-alt'></i>
        </div>
        <button type="submit" class="btn">Daftar</button>
    </form>
  </div>
</body>
</html>
