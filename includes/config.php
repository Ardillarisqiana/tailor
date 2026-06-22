<?php
// Koneksi ke database
// Yang perlu diganti cuma password kalau beda, biasanya kosong/password
$host = 'localhost';
$user = 'root';      // default XAMPP username
$pass = '';          // default XAMPP password = KOSONG! (kalau error coba ganti 'root')
$db   = 'db_jahitlink';

$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Mulai session buat login
session_start();
?>