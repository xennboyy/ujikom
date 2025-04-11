<?php
require '../includes/koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Error: User tidak terautentikasi!");
}

if (!isset($_GET['daftar_id'])) {
    die("Error: ID daftar tidak valid!");
}

$daftar_id = $_GET['daftar_id'];
$user_id = $_SESSION['user_id'];

// Hapus semua tugas dalam daftar
$conn->query("DELETE FROM tugas WHERE daftar_id = $daftar_id");

// Hapus daftar itu sendiri
if ($conn->query("DELETE FROM daftar WHERE id = $daftar_id AND user_id = $user_id")) {
    header("Location: ../daftar/daftar.php");
    exit();
} else {
    echo "Gagal menghapus daftar!";
}
?>
