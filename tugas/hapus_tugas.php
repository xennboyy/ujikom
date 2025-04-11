<?php
require '../includes/koneksi.php';
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Cek apakah tugas benar-benar milik user
    $stmt = $conn->prepare("SELECT daftar_id FROM tugas WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("Error: ID tugas atau daftar tidak valid.");
    }

    $row = $result->fetch_assoc();
    $daftar_id = $row['daftar_id']; // Simpan daftar_id untuk redirect nanti

    // Hapus tugas
    $stmt = $conn->prepare("DELETE FROM tugas WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    
    if ($stmt->execute()) {
        header("Location: tugas.php?daftar_id=$daftar_id"); // Redirect ke daftar yang sesuai
        exit();
    } else {
        echo "Gagal menghapus tugas!";
    }
} else {
    echo "ID tugas tidak valid.";
}
?>
