<?php
include '../includes/koneksi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'])) {
        // Step 1: Verifikasi Email
        $email = $_POST['email'];
        $check_email = "SELECT * FROM user WHERE email = '$email'";
        $result = $conn->query($check_email);

        if ($result->num_rows > 0) {
            // Email ditemukan, simpan ke session untuk proses reset
            $_SESSION['reset_email'] = $email;
            echo "<script>
                    alert('Email ditemukan! Masukkan password baru.');
                    window.location.href = 'lupa_password.php?step=reset';
                  </script>";
        } else {
            echo "<script>
                    alert('Email tidak ditemukan!');
                    window.location.href = 'lupa_password.php';
                  </script>";
        }
    } elseif (isset($_POST['new_password']) && isset($_SESSION['reset_email'])) {
        // Step 2: Reset Password
        $new_password = md5($_POST['new_password']);
        $email = $_SESSION['reset_email'];

        $update_password = "UPDATE user SET password = '$new_password' WHERE email = '$email'";
        if ($conn->query($update_password) === TRUE) {
            unset($_SESSION['reset_email']); // Hapus session reset
            echo "<script>
                    alert('Password berhasil diubah! Silakan login.');
                    window.location.href = 'login_page.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal mengubah password!');
                    window.location.href = 'lupa_password.php';
                  </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/login.css">
    <title>Lupa Password</title>
</head>
<body>
  <div class="wrapper">
    <?php if (!isset($_GET['step'])) { ?>
      <!-- Form Masukkan Email -->
      <form action="lupa_password.php" method="POST">
          <h1>Lupa Password</h1>
          <div class="login-box">
              <input type="email" name="email" placeholder="Masukkan email Anda" required>
              <i class='bx bx-envelope'></i>
          </div>
          <button type="submit" class="btn">Kirim</button>
      </form>
    <?php } elseif ($_GET['step'] == 'reset') { ?>
      <!-- Form Reset Password -->
      <form action="lupa_password.php" method="POST">
          <h1>Reset Password</h1>
          <div class="login-box">
              <input type="password" name="new_password" placeholder="Password baru" required>
              <i class='bx bx-lock-alt'></i>
          </div>
          <button type="submit" class="btn">Reset Password</button>
      </form>
    <?php } ?>
  </div>
</body>
</html>
