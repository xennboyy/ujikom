<?php
session_start();
include '../includes/koneksi.php'; // Pastikan koneksi ke database

$user_id = $_SESSION['user_id'];
$query = $conn->query("SELECT * FROM user WHERE id = '$user_id'");
$data = $query->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="../assets/dashboard.css">
    <link rel="stylesheet" href="../assets/profil.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-contentt">
        <h2>Profil</h2>
        <table>
            <tr>
                <td>ID</td>
                <td>: <?php echo $data['id']; ?></td>
            </tr>
            <tr>
                <td>Username</td>
                <td>: <?php echo $data['username']; ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td>: <?php echo $data['email']; ?></td>
            </tr>
        </table>
        <a href="logout.php" onclick="return confirm('Yakin ingin logout?');" class="btnn">Logout</a>
    </div>
</body>
</html>
