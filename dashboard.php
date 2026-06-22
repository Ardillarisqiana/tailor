<?php 
include 'includes/config.php'; 
include 'includes/auth.php'; 
include 'includes/header.php'; 
?>

<div class="card">
    <h1>👋 Selamat Datang, <?php echo $user['nama_lengkap']; ?>!</h1>
    
    <?php if($user['status'] == 'approved'): ?>
        <div class="alert alert-success">✅ Akun Anda sudah aktif! Portfolio Anda bisa dilihat publik.</div>
    <?php elseif($user['status'] == 'pending'): ?>
        <div class="alert alert-info">⏳ Akun Anda menunggu verifikasi admin. Portfolio belum bisa dilihat orang lain.</div>
    <?php else: ?>
        <div class="alert alert-error">❌ Akun Anda ditolak. Hubungi admin untuk info lebih lanjut.</div>
    <?php endif; ?>
    
    <div class="info-badge">📱 WhatsApp: <?php echo $user['whatsapp']; ?></div>
    <div class="info-badge">🔗 Link Portfolio: <a href="portofolio.php?id=<?php echo $user['id']; ?>">Klik lihat portfolio</a></div>
</div>

<div class="card">
    <h2>📸 Kelola Portfolio</h2>
    <button class="btn" onclick="showAddForm()" style="margin-bottom: 20px;">+ Tambah Karya Baru</button>
    
    <!-- FORM TAMBAH -->
    <div id="addForm" style="display: none; margin-top: 20px; padding: 25px; background: #f8f9fa; border-radius: 16px;">
        <h3>Tambah Karya Baru</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_portfolio">
            <div class="form-group">
                <label>Judul Karya</label>
                <input type="text" name="judul" required placeholder="Contoh: Gaun Pengantin Modern">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="3" placeholder="Ceritakan tentang karya ini..."></textarea>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori">
                    <option value="Pengantin">👰 Pengantin</option>
                    <option value="Pakaian Sehari">👕 Pakaian Sehari</option>
                    <option value="Seragam">👔 Seragam</option>
                    <option value="Batik">🦋 Batik</option>
                    <option value="Muslim">🧕 Busana Muslim</option>
                </select>
            </div>
            <div class="form-group">
                <label>Foto Karya *</label>
                <input type="file" name="foto" accept="image/*" required>
            </div>
            <div class="form-group">
                <label>Harga Estimasi (Rp)</label>
                <input type="number" name="harga_estimasi" placeholder="Contoh: 500000">
            </div>
            <button type="submit" class="btn">✨ Simpan Karya ✨</button>
            <button type="button" class="btn btn-danger" onclick="hideAddForm()">Batal</button>
        </form>
    </div>

    <!-- FORM EDIT (Muncul ketika ada parameter edit) -->
    <?php
    $edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
    if($edit_id > 0):
        $edit_query = "SELECT * FROM portfolio WHERE id = $edit_id AND penjahit_id = {$user['id']}";
        $edit_result = mysqli_query($conn, $edit_query);
        $edit_item = mysqli_fetch_assoc($edit_result);
        
        if($edit_item):
    ?>
    <div id="editForm" style="margin-top: 20px; padding: 25px; background: #fff3e0; border-radius: 16px; border: 2px solid #ff9800;">
        <h3>✏️ Edit Karya</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit_portfolio">
            <input type="hidden" name="edit_id" value="<?php echo $edit_item['id']; ?>">
            <div class="form-group">
                <label>Judul Karya</label>
                <input type="text" name="judul" required value="<?php echo htmlspecialchars($edit_item['judul']); ?>">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="3"><?php echo htmlspecialchars($edit_item['deskripsi']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori">
                    <option value="Pengantin" <?php echo ($edit_item['kategori'] == 'Pengantin') ? 'selected' : ''; ?>>👰 Pengantin</option>
                    <option value="Pakaian Sehari" <?php echo ($edit_item['kategori'] == 'Pakaian Sehari') ? 'selected' : ''; ?>>👕 Pakaian Sehari</option>
                    <option value="Seragam" <?php echo ($edit_item['kategori'] == 'Seragam') ? 'selected' : ''; ?>>👔 Seragam</option>
                    <option value="Batik" <?php echo ($edit_item['kategori'] == 'Batik') ? 'selected' : ''; ?>>🦋 Batik</option>
                    <option value="Muslim" <?php echo ($edit_item['kategori'] == 'Muslim') ? 'selected' : ''; ?>>🧕 Busana Muslim</option>
                </select>
            </div>
            <div class="form-group">
                <label>Foto Karya</label>
                <?php if($edit_item['foto'] && file_exists("uploads/" . $edit_item['foto'])): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="uploads/<?php echo $edit_item['foto']; ?>" style="width: 150px; border-radius: 8px;">
                        <p style="font-size: 12px;">Foto saat ini</p>
                    </div>
                <?php endif; ?>
                <input type="file" name="foto" accept="image/*">
                <small>Kosongkan jika tidak ingin mengganti foto</small>
            </div>
            <div class="form-group">
                <label>Harga Estimasi (Rp)</label>
                <input type="number" name="harga_estimasi" value="<?php echo $edit_item['harga_estimasi']; ?>">
            </div>
            <button type="submit" class="btn" style="background: #ff9800;">💾 Update Karya</button>
            <a href="dashboard.php" class="btn btn-danger">Batal</a>
        </form>
    </div>
    <?php 
        endif;
    endif;
    ?>

    <?php
    // PROSES TAMBAH PORTFOLIO
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_portfolio') {
        $judul = mysqli_real_escape_string($conn, $_POST['judul']);
        $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
        $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
        $harga_estimasi = (int)$_POST['harga_estimasi'];
        
        $foto = '';
        if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $foto = time() . '_' . basename($_FILES['foto']['name']);
            move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto);
        }
        
        $query = "INSERT INTO portfolio (penjahit_id, judul, deskripsi, kategori, foto, harga_estimasi) 
                  VALUES ({$user['id']}, '$judul', '$deskripsi', '$kategori', '$foto', $harga_estimasi)";
        
        if(mysqli_query($conn, $query)) {
            echo '<div class="alert alert-success">✅ Karya berhasil ditambahkan!</div>';
            echo '<meta http-equiv="refresh" content="1">';
        } else {
            echo '<div class="alert alert-error">❌ Error: ' . mysqli_error($conn) . '</div>';
        }
    }
    
    // PROSES EDIT PORTFOLIO
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit_portfolio') {
        $edit_id = (int)$_POST['edit_id'];
        $judul = mysqli_real_escape_string($conn, $_POST['judul']);
        $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
        $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
        $harga_estimasi = (int)$_POST['harga_estimasi'];
        
        // Ambil foto lama
        $query_foto = "SELECT foto FROM portfolio WHERE id = $edit_id AND penjahit_id = {$user['id']}";
        $result_foto = mysqli_query($conn, $query_foto);
        $foto_lama = mysqli_fetch_assoc($result_foto);
        $foto = $foto_lama['foto'];
        
        // Cek upload foto baru
        if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $foto_baru = time() . '_' . basename($_FILES['foto']['name']);
            move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto_baru);
            // Hapus foto lama
            if($foto && file_exists("uploads/" . $foto)) {
                unlink("uploads/" . $foto);
            }
            $foto = $foto_baru;
        }
        
        $update = "UPDATE portfolio SET 
                    judul = '$judul',
                    deskripsi = '$deskripsi',
                    kategori = '$kategori',
                    harga_estimasi = $harga_estimasi,
                    foto = '$foto'
                   WHERE id = $edit_id AND penjahit_id = {$user['id']}";
        
        if(mysqli_query($conn, $update)) {
            echo '<div class="alert alert-success">✅ Karya berhasil diupdate!</div>';
            echo '<meta http-equiv="refresh" content="1;url=dashboard.php">';
        } else {
            echo '<div class="alert alert-error">❌ Error: ' . mysqli_error($conn) . '</div>';
        }
    }
    
    // Ambil semua portfolio
    $query = "SELECT * FROM portfolio WHERE penjahit_id = {$user['id']} ORDER BY dibuat_pada DESC";
    $result = mysqli_query($conn, $query);
    ?>
    
    <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="portfolio-grid" style="margin-top: 30px;">
            <?php while($item = mysqli_fetch_assoc($result)): ?>
            <div class="portfolio-card">
                <img src="uploads/<?php echo $item['foto']; ?>" alt="<?php echo $item['judul']; ?>">
                <div class="info">
                    <h3><?php echo $item['judul']; ?></h3>
                    <p><?php echo substr($item['deskripsi'], 0, 80); ?>...</p>
                    <p><strong>💰 Rp <?php echo number_format($item['harga_estimasi'], 0, ',', '.'); ?></strong></p>
                    <div style="margin-top: 10px;">
                        <a href="dashboard.php?edit=<?php echo $item['id']; ?>" class="btn" style="background: #ff9800; font-size: 14px; padding: 8px 15px; margin-right: 5px; display: inline-block;">✏️ Edit</a>
                        <a href="hapus_portofolio.php?id=<?php echo $item['id']; ?>" class="btn btn-danger" style="font-size: 14px; padding: 8px 15px; display: inline-block;" onclick="return confirm('Yakin mau hapus karya ini?')">🗑️ Hapus</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info" style="margin-top: 20px;">Belum ada portfolio. Klik "Tambah Karya Baru" untuk mulai upload!</div>
    <?php endif; ?>
</div>

<!-- ========== MANAJEMEN ORDER ========== -->
<div class="card" style="margin-top: 30px;">
    <h2>Manajemen Order</h2>
    <p>Maksimal order aktif: <strong>8</strong> per bulan</p>
    
    <?php
    // Hitung order aktif
    $aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE penjahit_id = {$user['id']} AND status IN ('pending', 'diterima')"));
    $sisa = 8 - $aktif['total'];
    echo "<div class='alert alert-info'>Sisa kuota bulan ini: <strong>$sisa</strong> dari 8 order</div>";
    
    // Proses update status
    if(isset($_GET['ubah_status'])) {
        $order_id = (int)$_GET['order_id'];
        $status = $_GET['ubah_status'];
        mysqli_query($conn, "UPDATE orders SET status = '$status' WHERE id = $order_id AND penjahit_id = {$user['id']}");
        echo '<script>alert("Status berhasil diubah!"); window.location.href="dashboard.php";</script>';
    }
    
    $orders = mysqli_query($conn, "SELECT * FROM orders WHERE penjahit_id = {$user['id']} ORDER BY nomor_order ASC");
    ?>
    
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
        <thead>
            <tr style="background: #1e3c72; color: white;">
                <th style="padding: 10px;">No</th>
                <th style="padding: 10px;">Klien</th>
                <th style="padding: 10px;">Jenis</th>
                <th style="padding: 10px;">Jml</th>
                <th style="padding: 10px;">Budget</th>
                <th style="padding: 10px;">Status</th>
                <th style="padding: 10px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($order = mysqli_fetch_assoc($orders)): ?>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px;"><?php echo $order['nomor_order']; ?></td>
                <td style="padding: 10px;">
                    <?php echo htmlspecialchars($order['nama_klien']); ?><br>
                    <small><?php echo $order['no_hp']; ?></small>
                </td>
                <td style="padding: 10px;"><?php echo $order['jenis_pakaian']; ?></td>
                <td style="padding: 10px;"><?php echo $order['jumlah']; ?> pcs</td>
                <td style="padding: 10px;">Rp <?php echo number_format($order['budget_estimasi'], 0, ',', '.'); ?></td>
                <td style="padding: 10px;">
                    <?php
                    $warna = match($order['status']) {
                        'pending' => 'orange',
                        'diterima' => 'green',
                        'ditolak' => 'red',
                        'selesai' => 'blue',
                        default => 'gray'
                    };
                    echo "<span style='background: $warna; color: white; padding: 4px 10px; border-radius: 20px; font-size: 12px;'>" . ucfirst($order['status']) . "</span>";
                    ?>
                </td>
                <td style="padding: 10px;">
                    <select onchange="if(confirm('Ubah status?')) window.location.href='dashboard.php?ubah_status='+this.value+'&order_id=<?php echo $order['id']; ?>'">
                        <option value="">Ubah ke...</option>
                        <option value="diterima" <?php echo $order['status']=='diterima'?'selected':''; ?>>✅ Diterima</option>
                        <option value="ditolak" <?php echo $order['status']=='ditolak'?'selected':''; ?>>❌ Ditolak</option>
                        <option value="selesai" <?php echo $order['status']=='selesai'?'selected':''; ?>>🎉 Selesai</option>
                    </select>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if(mysqli_num_rows($orders) == 0): ?>
            <tr><td colspan="7" style="text-align: center; padding: 30px;">Belum ada order</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function showAddForm() {
    document.getElementById('addForm').style.display = 'block';
}
function hideAddForm() {
    document.getElementById('addForm').style.display = 'none';
}
</script>

<?php include 'includes/footer.php'; ?>