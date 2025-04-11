<?php
require '../includes/koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Error: User tidak ditemukan. Silakan login kembali.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['nama_tugas'], $_POST['prioritas'], $_POST['deadline'], $_POST['daftar_id'])) {
        die("Error: Form belum lengkap!");
    }

    $daftar_id = $_POST['daftar_id']; // Ambil daftar_id dari form
    $nama_tugas = htmlspecialchars($_POST['nama_tugas']);
    $prioritas = $_POST['prioritas'];
    $deadline = $_POST['deadline'];
    $status = "Belum Selesai"; 
    $user_id = $_SESSION['user_id'];

    // Pastikan deadline tidak di masa lalu
    if ($deadline < date('Y-m-d')) {
        die("Error: Deadline tidak boleh di masa lalu!");
    }

    // Pastikan daftar_id benar-benar milik user yang login
    $cek_daftar = $conn->prepare("SELECT id FROM daftar WHERE id = ? AND user_id = ?");
    $cek_daftar->bind_param("ii", $daftar_id, $user_id);
    $cek_daftar->execute();
    $cek_daftar->store_result();

    if ($cek_daftar->num_rows == 0) {
        die("Error: Daftar tidak ditemukan atau bukan milik Anda!");
    }

    // Insert tugas ke dalam daftar yang sesuai
    $stmt = $conn->prepare("INSERT INTO tugas (daftar_id, nama_tugas, prioritas, deadline, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $daftar_id, $nama_tugas, $prioritas, $deadline, $status);

    if ($stmt->execute()) {
        header("Location: tugas.php?daftar_id=$daftar_id");
        exit();
    } else {
        echo "Gagal menambahkan tugas!";
    }
}
?>