<?php
include 'includes/config.php';
include 'includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Cek apakah portfolio milik user yang login
$query = "SELECT foto FROM portfolio WHERE id = $id AND penjahit_id = {$user['id']}";
$result = mysqli_query($conn, $query);
$item = mysqli_fetch_assoc($result);

if($item) {
    // Hapus file foto
    if($item['foto'] && file_exists("uploads/" . $item['foto'])) {
        unlink("uploads/" . $item['foto']);
    }
    
    // Hapus dari database
    $delete = "DELETE FROM portfolio WHERE id = $id";
    mysqli_query($conn, $delete);
}

header("Location: dashboard.php");
exit();
?>