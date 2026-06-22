<?php 
include 'includes/config.php';
include 'includes/header.php';

// Password admin
$admin_password = "admin123";

// Cek apakah sudah login sebagai admin
if(!isset($_SESSION['admin_login'])) {
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_pass'])) {
        if($_POST['admin_pass'] == $admin_password) {
            $_SESSION['admin_login'] = true;
        } else {
            $error = "Password salah!";
        }
    }
    
    if(!isset($_SESSION['admin_login'])):
?>
    <div class="form-container">
        <div class="form-card">
            <h1 style="text-align: center;">🔐 Admin Login</h1>
            <?php if(isset($error)) echo '<div class="alert alert-error">'.$error.'</div>'; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Password Admin</label>
                    <input type="password" name="admin_pass" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>
    </div>
<?php 
        include 'includes/footer.php';
        exit();
    endif;
}

// PROSES APPROVE
if(isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    mysqli_query($conn, "UPDATE penjahit SET status = 'approved' WHERE id = $id");
    echo '<script>alert("✅ Penjahit berhasil disetujui!"); window.location.href="admin.php";</script>';
}

// PROSES REJECT
if(isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    mysqli_query($conn, "UPDATE penjahit SET status = 'rejected' WHERE id = $id");
    echo '<script>alert("❌ Penjahit ditolak!"); window.location.href="admin.php";</script>';
}

// HAPUS PENJAHIT
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM portfolio WHERE penjahit_id = $id");
    mysqli_query($conn, "DELETE FROM testimoni WHERE penjahit_id = $id");
    mysqli_query($conn, "DELETE FROM pertanyaan WHERE penjahit_id = $id");
    mysqli_query($conn, "DELETE FROM penjahit WHERE id = $id");
    echo '<script>alert("🗑️ Penjahit berhasil dihapus!"); window.location.href="admin.php";</script>';
}

// Ambil semua penjahit
$query = "SELECT * FROM penjahit ORDER BY dibuat_pada DESC";
$result = mysqli_query($conn, $query);

$total = mysqli_num_rows($result);
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM penjahit WHERE status = 'pending'"));
$approved = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM penjahit WHERE status = 'approved'"));
$rejected = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM penjahit WHERE status = 'rejected'"));
?>

<div class="card">
    <h1 style="margin-bottom: 5px;">👑 Admin Panel</h1>
    <p style="color: #666; margin-bottom: 20px;">Kelola semua data penjahit</p>
    
    <!-- STATISTIK -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px;">
        <div style="background: linear-gradient(135deg, #1e3c72, #2a5298); color: white; padding: 15px; border-radius: 12px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold;"><?php echo $total; ?></div>
            <div style="font-size: 14px;">Total Penjahit</div>
        </div>
        <div style="background: linear-gradient(135deg, #e67e22, #f39c12); color: white; padding: 15px; border-radius: 12px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold;"><?php echo $pending; ?></div>
            <div style="font-size: 14px;">Menunggu</div>
        </div>
        <div style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; padding: 15px; border-radius: 12px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold;"><?php echo $approved; ?></div>
            <div style="font-size: 14px;">Disetujui</div>
        </div>
        <div style="background: linear-gradient(135deg, #c0392b, #e74c3c); color: white; padding: 15px; border-radius: 12px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold;"><?php echo $rejected; ?></div>
            <div style="font-size: 14px;">Ditolak</div>
        </div>
    </div>
    
    <!-- TABEL -->
    <div style="overflow-x: auto; border-radius: 12px; border: 1px solid #e0e0e0;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #e0e0e0;">
                    <th style="padding: 12px; text-align: left;">ID</th>
                    <th style="padding: 12px; text-align: left;">Foto</th>
                    <th style="padding: 12px; text-align: left;">Nama</th>
                    <th style="padding: 12px; text-align: left;">Email</th>
                    <th style="padding: 12px; text-align: left;">WhatsApp</th>
                    <th style="padding: 12px; text-align: left;">Toko</th>
                    <th style="padding: 12px; text-align: left;">Spesialisasi</th>
                    <th style="padding: 12px; text-align: left;">Harga</th>
                    <th style="padding: 12px; text-align: left;">Status</th>
                    <th style="padding: 12px; text-align: left;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                mysqli_data_seek($result, 0);
                while($row = mysqli_fetch_assoc($result)): 
                ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px 12px;"><?php echo $row['id']; ?></td>
                    <td style="padding: 10px 12px;">
                        <?php if($row['foto_profil'] && file_exists("uploads/" . $row['foto_profil'])): ?>
                            <img src="uploads/<?php echo $row['foto_profil']; ?>" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 35px; height: 35px; background: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center;">✂️</div>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 10px 12px;">
                        <strong><?php echo htmlspecialchars($row['nama_lengkap']); ?></strong><br>
                        <small style="color: #888;">@<?php echo $row['username']; ?></small>
                    </td>
                    <td style="padding: 10px 12px;"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td style="padding: 10px 12px;">
                        <a href="https://wa.me/<?php echo $row['whatsapp']; ?>" target="_blank" style="color: #25D366; text-decoration: none;"><?php echo $row['whatsapp']; ?></a>
                    </td>
                    <td style="padding: 10px 12px;"><?php echo htmlspecialchars($row['nama_toko'] ?: '-'); ?></td>
                    <td style="padding: 10px 12px;"><?php echo htmlspecialchars($row['spesialisasi']); ?></td>
                    <td style="padding: 10px 12px;">Rp <?php echo number_format($row['harga_minimal'], 0, ',', '.'); ?></td>
                    <td style="padding: 10px 12px;">
                        <?php 
                        if($row['status'] == 'approved') {
                            echo '<span style="background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 20px; font-size: 11px;">✅ Disetujui</span>';
                        } elseif($row['status'] == 'pending') {
                            echo '<span style="background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 20px; font-size: 11px;">⏳ Menunggu</span>';
                        } else {
                            echo '<span style="background: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 20px; font-size: 11px;">❌ Ditolak</span>';
                        }
                        ?>
                    </td>
                    <td style="padding: 10px 12px;">
                        <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <a href="admin.php?approve=<?php echo $row['id']; ?>" class="btn" style="background: #27ae60; padding: 4px 10px; font-size: 11px; color: white;">✅</a>
                            <a href="admin.php?reject=<?php echo $row['id']; ?>" class="btn btn-danger" style="padding: 4px 10px; font-size: 11px;">❌</a>
                            <a href="admin.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger" style="background: #888; padding: 4px 10px; font-size: 11px;" onclick="return confirm('Yakin hapus?')">🗑️</a>
                            <a href="portofolio.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn" style="background: #2196F3; padding: 4px 10px; font-size: 11px;">👁️</a>
                        </div>
                    </td>
                    <th style="padding: 12px;">⭐ Rating</th>
                    <td style="padding: 10px 12px;">
                        <?php 
                        $rata = round($row['rating_total'] / max($row['jumlah_rating'], 1), 1);
                        echo $rata . ' ⭐ (' . $row['jumlah_rating'] . ')';
                        ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>