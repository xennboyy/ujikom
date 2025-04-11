<?php
session_start();
session_unset();
session_destroy();
setcookie(session_name(), '', time() - 3600, '/'); // Hapus cookie sesi
header("Location: ../login/login_page.php");
exit();
?>
