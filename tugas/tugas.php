<?php
require '../includes/koneksi.php';
session_start(); // Pastikan session dimulai

if (!isset($_SESSION['user_id'])) {
    die("Error: User tidak terautentikasi!");
}

$user_id = $_SESSION['user_id']; // Ambil user_id dari session

if (!isset($_GET['daftar_id'])) {
    die("Daftar tidak ditemukan!");
}

$daftar_id = $_GET['daftar_id'];

// Ambil nama daftar
$daftar = $conn->query("SELECT * FROM daftar WHERE id = $daftar_id");
if ($daftar->num_rows == 0) {
    die("Daftar tidak ditemukan!");
}
$nama_list = $daftar->fetch_assoc()['nama_list'];

// Proses tambah tugas
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_tugas = htmlspecialchars($_POST['nama_tugas']);
    $prioritas = $_POST['prioritas'];
    $deadline = $_POST['deadline'];

    if ($deadline < date('Y-m-d')) {
        die("Error: Deadline tidak boleh di masa lalu!");
    }

    $status = "Belum Selesai";
    $stmt = $conn->prepare("INSERT INTO tugas (daftar_id, nama_tugas, prioritas, deadline, status, user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssi", $daftar_id, $nama_tugas, $prioritas, $deadline, $status, $user_id);

    if ($stmt->execute()) {
        header("Location: tugas.php?daftar_id=" . $_GET['daftar_id']);
        exit();
    } else {
        echo "Gagal menambahkan tugas!";
    }
}

// Ambil semua tugas dalam daftar ini
$result = $conn->query("SELECT * FROM tugas WHERE daftar_id = $daftar_id ORDER BY 
    CASE 
        WHEN prioritas = 'Penting' THEN 1 
        WHEN prioritas = 'Sedikit Penting' THEN 2 
        ELSE 3 
    END, dibuat_pada DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nama_list); ?></title>
    <link rel="stylesheet" href="../assets/tugas.css">
    <style>
        .deadline-hijau { color: green; font-weight: bold; }
        .deadline-merah { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <!-- Header Daftar -->
    <div class="header-daftar">
        <h2 class="judul-daftar"><?= htmlspecialchars($nama_list); ?></h2>
        <div class="daftar-actions">
            <a href="../daftar/edit_daftar.php?daftar_id=<?= $daftar_id; ?>" class="btn-edit">Edit</a>
            <a href="../daftar/hapus_daftar.php?daftar_id=<?= $daftar_id; ?>" class="btn-delete" onclick="return confirm('Hapus daftar ini? Semua tugas akan ikut terhapus!');">Hapus</a>
        </div>
    </div>

    <!-- Form Tambah Tugas -->
    <div class="form-container">
        <form action="" method="POST" class="form-tugas">
            <input type="text" name="nama_tugas" placeholder="Nama Tugas" required>
            <select name="prioritas">
                <option value="Penting">Penting</option>
                <option value="Sedikit Penting">Sedikit Penting</option>
                <option value="Tidak Terlalu Penting">Tidak Terlalu Penting</option>
            </select>
            <input type="date" name="deadline" required>
            <button type="submit">Tambah Tugas</button>
        </form>
    </div>

    <!-- Tabel Daftar Tugas -->
    <div class="table-container">
        <table class="tugas-table">
            <thead>
                <tr>
                    <th>Nama Tugas</th>
                    <th>Prioritas</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                    $deadline = $row['deadline'];
                    $status = $row['status'];
                    $hari_ini = date('Y-m-d');

                    // Logika warna deadline
                    if ($status == "Selesai" && $deadline >= $hari_ini) {
                        $deadline_class = "deadline-hijau"; // Selesai sebelum deadline = hijau
                    } elseif ($deadline < $hari_ini && $status == "Belum Selesai") {
                        $deadline_class = "deadline-merah"; // Terlewat & belum selesai = merah
                    } elseif ($deadline < $hari_ini && $status == "Selesai") {
                        $deadline_class = "deadline-merah"; // Terlewat & selesai setelah deadline = merah
                    } else {
                        $deadline_class = ""; // Default (warna putih)
                    }
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_tugas']); ?></td>
                    <td><?= $row['prioritas']; ?></td>
                    <td class="<?= $deadline_class; ?>"><?= date("d-m-Y", strtotime($row['deadline'])); ?></td>
                    <td><?= $row['status']; ?></td>

                    <td class="task-actions">
                        <?php if ($row['status'] == "Belum Selesai"): ?>
                            <a href="ubah_status.php?id=<?= $row['id']; ?>&status=Selesai&daftar_id=<?= $daftar_id; ?>" class="task-done"> Tandai Selesai </a>
                        <?php else: ?>
                            <a href="ubah_status.php?id=<?= $row['id']; ?>&status=Belum Selesai&daftar_id=<?= $daftar_id; ?>" class="task-undone"> Tandai Belum Selesai </a>
                        <?php endif; ?>
                        <a href="hapus_tugas.php?id=<?= $row['id']; ?>" class="task-delete"> Hapus </a> 
                        <a href="edit_tugas.php?tugas_id=<?= $row['id']; ?>" class="task-edit"> Edit </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Navigasi Kembali -->
    <a href="../daftar/daftar.php" class="back-to-list">⬅ Kembali ke Daftar</a>

</body>
</html>
