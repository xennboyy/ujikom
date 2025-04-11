<?php 
require '../includes/koneksi.php';
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Pastikan parameter yang dibutuhkan tersedia
if (isset($_GET['id']) && isset($_GET['status']) && isset($_GET['daftar_id'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'];
    $daftar_id = intval($_GET['daftar_id']); // Ambil daftar_id dari URL
    $user_id = $_SESSION['user_id'];

    // Pastikan status hanya bisa diubah ke "Selesai" atau "Belum Selesai"
    if ($status === "Selesai" || $status === "Belum Selesai") {
        // Pastikan tugas milik user yang sedang login
        $stmt = $conn->prepare("UPDATE tugas SET status = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $status, $id, $user_id);

        if ($stmt->execute()) {
            header("Location: tugas.php?daftar_id=$daftar_id"); // ✅ Redirect dengan daftar_id
            exit();
        } else {
            echo "Gagal memperbarui status!";
        }
    } else {
        echo "Status tidak valid!";
    }
} else {
    echo "Error: ID, daftar_id, atau status tidak ditemukan!";
}
?>
