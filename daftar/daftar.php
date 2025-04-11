<?php
require '../includes/koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Proses tambah daftar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_list = htmlspecialchars($_POST['nama_list']);
    
    $stmt = $conn->prepare("INSERT INTO daftar (nama_list, user_id) VALUES (?, ?)");
    $stmt->bind_param("si", $nama_list, $user_id);
    
    if ($stmt->execute()) {
        header("Location: daftar.php");
        exit();
    } else {
        echo "Gagal menambahkan daftar!";
    }
}

// Ambil semua daftar tugas milik user
$result = $conn->query("SELECT * FROM daftar WHERE user_id = $user_id ORDER BY dibuat_pada DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas</title>
    <link rel="stylesheet" href="../assets/daftar.css"> <!-- Pakai CSS baru -->
</head>
<body>

<div class="container">
    <h2>📋 Daftar Tugas</h2>

    <!-- Form Tambah Daftar -->
    <form action="" method="POST">
        <input type="text" name="nama_list" placeholder="Tambah Daftar Baru..." required>
        <button type="submit">Tambah</button>
    </form>

    <!-- List Daftar -->
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <a href="../tugas/tugas.php?daftar_id=<?= $row['id']; ?>">
                    <?= htmlspecialchars($row['nama_list']); ?>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>

    <!-- Tombol Dashboard -->
    <a href="../dashboard/index.php" class="dashboard-btn">Dashboard</a>
</div>

</body>
</html>
