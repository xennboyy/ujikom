<?php
require '../includes/koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Error: User tidak terautentikasi!");
}

if (!isset($_GET['daftar_id'])) {
    die("Error: Daftar tidak ditemukan!");
}

$daftar_id = $_GET['daftar_id'];
$user_id = $_SESSION['user_id'];

// Ambil data daftar berdasarkan ID dan user
$query = $conn->prepare("SELECT * FROM daftar WHERE id = ? AND user_id = ?");
$query->bind_param("ii", $daftar_id, $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows == 0) {
    die("Daftar tidak ditemukan!");
}

$daftar = $result->fetch_assoc();

// Jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_list = htmlspecialchars($_POST['nama_list']);

    $stmt = $conn->prepare("UPDATE daftar SET nama_list = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $nama_list, $daftar_id, $user_id);

    if ($stmt->execute()) {
        header("Location: daftar.php");
        exit();
    } else {
        echo "Gagal mengupdate daftar!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Daftar</title>
    <link rel="stylesheet" href="../assets/edit_daftar.css">
</head>
<body>
    <div class="edit-container">
        <h2>Edit Daftar</h2>
        <form method="POST" class="edit-form">
            <label for="nama_list">Nama Daftar</label>
            <input type="text" name="nama_list" id="nama_list" value="<?= htmlspecialchars($daftar['nama_list']); ?>" required>

            <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
            <a href="daftar.php" class="btn-cancel">❌ Batal</a>
        </form>
    </div>
</body>
</html>
