<?php
require '../includes/koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Error: User tidak terautentikasi!");
}

if (!isset($_GET['tugas_id'])) {
    die("Error: Tugas tidak ditemukan!");
}

$tugas_id = $_GET['tugas_id'];
$user_id = $_SESSION['user_id'];

// Ambil data tugas berdasarkan ID
$query = $conn->prepare("SELECT * FROM tugas WHERE id = ? AND user_id = ?");
$query->bind_param("ii", $tugas_id, $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows == 0) {
    die("Tugas tidak ditemukan!");
}

$tugas = $result->fetch_assoc();

// Jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_tugas = htmlspecialchars($_POST['nama_tugas']);
    $prioritas = $_POST['prioritas'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE tugas SET nama_tugas = ?, prioritas = ?, deadline = ?, status = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssssii", $nama_tugas, $prioritas, $deadline, $status, $tugas_id, $user_id);

    if ($deadline < date('Y-m-d')) {
        die("Error: Deadline tidak boleh di masa lalu!");
    }

    if ($stmt->execute()) {
        header("Location: tugas.php?daftar_id=" . $tugas['daftar_id']);
        exit();
    } else {
        echo "Gagal mengupdate tugas!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tugas</title>
    <link rel="stylesheet" href="../assets/edit_tugas.css">
</head>
<body>
    <div class="edit-container">
        <h2>Edit Tugas</h2>
        <form method="POST" class="edit-form">
            <label for="nama_tugas">Nama Tugas</label>
            <input type="text" name="nama_tugas" id="nama_tugas" value="<?= htmlspecialchars($tugas['nama_tugas']); ?>" required>

            <label for="prioritas">Prioritas</label>
            <select name="prioritas" id="prioritas">
                <option value="Penting" <?= ($tugas['prioritas'] == "Penting") ? "selected" : ""; ?>>Penting</option>
                <option value="Sedikit Penting" <?= ($tugas['prioritas'] == "Sedikit Penting") ? "selected" : ""; ?>>Sedikit Penting</option>
                <option value="Tidak Terlalu Penting" <?= ($tugas['prioritas'] == "Tidak Terlalu Penting") ? "selected" : ""; ?>>Tidak Terlalu Penting</option>
            </select>

            <label for="deadline">Deadline</label>
            <input type="date" name="deadline" id="deadline" value="<?= $tugas['deadline']; ?>" required>

            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="Belum Selesai" <?= ($tugas['status'] == "Belum Selesai") ? "selected" : ""; ?>>Belum Selesai</option>
                <option value="Selesai" <?= ($tugas['status'] == "Selesai") ? "selected" : ""; ?>>Selesai</option>
            </select>

            <button type="submit" class="btn-save">Simpan Perubahan</button>
            <a href="tugas.php?daftar_id=<?= $tugas['daftar_id']; ?>" class="btn-cancel">Batal</a>
        </form>
    </div>
</body>
</html>
