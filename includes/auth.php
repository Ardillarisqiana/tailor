<?php
// File ini buat cek apakah user sudah login atau belum

// Cek apakah session sudah dimulai
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login (ada session user_id)
if(!isset($_SESSION['user_id'])) {
    // Kalau belum login, lempar ke halaman login
    header("Location: login.php");
    exit();
}

// Ambil data user yang login dari database
$user_id = $_SESSION['user_id'];
$stmt = mysqli_prepare($conn, "SELECT * FROM penjahit WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Kalau user tidak ditemukan di database
if(!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>