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
$query = "SELECT * FROM penjahit WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Kalau user tidak ditemukan di database
if(!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>