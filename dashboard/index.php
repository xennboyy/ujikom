<?php
require '../includes/koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}
;

$stmt_daftar = $conn->prepare("SELECT * FROM daftar WHERE user_id = ? ORDER BY dibuat_pada DESC LIMIT 5");
$stmt_daftar->bind_param("i", $user_id);
$stmt_daftar->execute();
$daftar_result = $stmt_daftar->get_result();

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Total daftar
$stmt_total_list = $conn->prepare("SELECT COUNT(*) AS total FROM daftar WHERE user_id = ?");
$stmt_total_list->bind_param("i", $user_id);
$stmt_total_list->execute();
$list_total = $stmt_total_list->get_result()->fetch_assoc()['total'];

// Total task di semua daftar milik user
$stmt_total_task = $conn->prepare("SELECT COUNT(*) AS total FROM tugas WHERE daftar_id IN (SELECT id FROM daftar WHERE user_id = ?)");
$stmt_total_task->bind_param("i", $user_id);
$stmt_total_task->execute();
$task_total = $stmt_total_task->get_result()->fetch_assoc()['total'];

// Task selesai
$stmt_task_selesai = $conn->prepare("SELECT COUNT(*) AS selesai FROM tugas WHERE daftar_id IN (SELECT id FROM daftar WHERE user_id = ?) AND status = 'Selesai'");
$stmt_task_selesai->bind_param("i", $user_id);
$stmt_task_selesai->execute();
$task_selesai = $stmt_task_selesai->get_result()->fetch_assoc()['selesai'];

// Task belum selesai
$stmt_task_belum = $conn->prepare("SELECT COUNT(*) AS belum FROM tugas WHERE daftar_id IN (SELECT id FROM daftar WHERE user_id = ?) AND status = 'Belum Selesai'");
$stmt_task_belum->bind_param("i", $user_id);
$stmt_task_belum->execute();
$task_belum = $stmt_task_belum->get_result()->fetch_assoc()['belum'];


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/dashboard.css">
    <title>Dashboard</title>
</head>
<body>
    <div class="sidebar">
    <?php include "sidebar.php"; ?>
    </div>
    <div class="main-content">
        <div class="navbar">
                <div class="navbar-left">
                    <h2>Dashboard</h2>
                </div>
                <div class="navbar-right">
                    <div class="user-dropdown">
                        <button class="dropdown-btn">Hi, <?php echo $_SESSION['username']; ?></button>
                        <div class="dropdown-content">
                            <a href="profil.php">Profil</a> 
                            <a href="logout.php" onclick="return confirm('Yakin ingin logout?');">Logout</a>

                        </div>
                    </div>
                </div>
        </div>
        <main>
        <div class="dashboard-cards">
            <div class="card">
                <h3>Total Daftar</h3>
                <p><?php echo $list_total; ?></p>
            </div>
            <div class="card">
                <h3>Total Task</h3>
                <p><?php echo $task_total; ?></p>
            </div>
            <div class="card selesai">
                <h3>Task Selesai</h3>
                <p><?php echo $task_selesai; ?></p>
            </div>
            <div class="card belum">
                <h3>Task Belum Selesai</h3>
                <p><?php echo $task_belum; ?></p>
            </div>
        </div>
        </main>
    </div>
</body>
</html>
