<?php 
include 'includes/config.php'; 
include 'includes/header.php'; 

$id_penjahit = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$penjahit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM penjahit WHERE id = $id_penjahit"));

if(!$penjahit) {
    echo "<div class='card'><h1>Penjahit tidak ditemukan</h1><a href='index.php' class='btn'>Kembali</a></div>";
    include 'includes/footer.php';
    exit();
}

// Hitung antrian aktif (pending + diterima)
$antrian = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE penjahit_id = $id_penjahit AND status IN ('pending', 'diterima')"));
$max_order = 8; // Maksimal 8 order aktif per bulan
$sisa_kuota = $max_order - $antrian['total'];
$next_number = mysqli_fetch_assoc(mysqli_query($conn, "SELECT last_number FROM order_sequence"))['last_number'] + 1;
?>

<div class="form-container">
    <div class="form-card">
        <h1 style="text-align: center;">📝 Form Pemesanan</h1>
        <p style="text-align: center;">Pesan baju ke <strong><?php echo htmlspecialchars($penjahit['nama_toko'] ?: $penjahit['nama_lengkap']); ?></strong></p>
        
        <?php if($sisa_kuota <= 0): ?>
            <div class="alert alert-error">
                ⚠️ Maaf, penjahit sedang penuh (maksimal <?php echo $max_order; ?> order aktif). Silakan coba bulan depan.
            </div>
        <?php else: ?>
            <div class="alert alert-info" style="background: #e3f2fd;">
                📊 Sisa kuota bulan ini: <strong><?php echo $sisa_kuota; ?></strong> dari <?php echo $max_order; ?> order<br>
                🎫 Nomor antrian Anda nanti: <strong><?php echo $next_number; ?></strong> (nomor tetap walau order lain dihapus)
            </div>
        <?php endif; ?>
        
        <?php
        if($_SERVER['REQUEST_METHOD'] == 'POST' && $sisa_kuota > 0) {
            $nama_klien = mysqli_real_escape_string($conn, $_POST['nama_klien']);
            $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
            $jenis_pakaian = mysqli_real_escape_string($conn, $_POST['jenis_pakaian']);
            $jumlah = (int)$_POST['jumlah'];
            $budget_estimasi = (int)$_POST['budget_estimasi'];
            $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
            
            // Upload foto referensi
            $foto_referensi = '';
            if(isset($_FILES['foto_referensi']) && $_FILES['foto_referensi']['error'] == 0) {
                $ext = pathinfo($_FILES['foto_referensi']['name'], PATHINFO_EXTENSION);
                $foto_referensi = time() . '_' . rand(1000,9999) . '.' . $ext;
                move_uploaded_file($_FILES['foto_referensi']['tmp_name'], "uploads/referensi/" . $foto_referensi);
            }
            
            // Ambil nomor urut berikutnya
            $nomor_order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT last_number FROM order_sequence"))['last_number'] + 1;
            
            $query = "INSERT INTO orders (nomor_order, penjahit_id, nama_klien, no_hp, jenis_pakaian, jumlah, foto_referensi, budget_estimasi, catatan, status) 
                      VALUES ($nomor_order, $id_penjahit, '$nama_klien', '$no_hp', '$jenis_pakaian', $jumlah, '$foto_referensi', $budget_estimasi, '$catatan', 'pending')";
            
            if(mysqli_query($conn, $query)) {
                mysqli_query($conn, "UPDATE order_sequence SET last_number = $nomor_order");
                echo '<div class="alert alert-success">';
                echo '✅ Pesanan berhasil! Nomor antrian Anda: <strong>' . $nomor_order . '</strong><br>';
                echo 'Penjahit akan menghubungi Anda segera via WhatsApp.';
                echo '</div>';
                echo '<meta http-equiv="refresh" content="3;url=portofolio.php?id=' . $id_penjahit . '">';
            } else {
                echo '<div class="alert alert-error">❌ Gagal: ' . mysqli_error($conn) . '</div>';
            }
        }
        ?>
        
        <?php if($sisa_kuota > 0): ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Anda *</label>
                <input type="text" name="nama_klien" required>
            </div>
            <div class="form-group">
                <label>Nomor HP/WhatsApp *</label>
                <input type="text" name="no_hp" required placeholder="081234567890">
            </div>
            <div class="form-group">
                <label>Jenis Pakaian *</label>
                <select name="jenis_pakaian" required>
                    <option value="">Pilih</option>
                    <option>Baju Pengantin</option>
                    <option>Baju Sehari-hari</option>
                    <option>Seragam</option>
                    <option>Batik</option>
                    <option>Busana Muslim</option>
                    <option>Pakaian Anak</option>
                    <option>Lainnya</option>
                </select>
            </div>
            <div class="form-group">
                <label>Jumlah *</label>
                <input type="number" name="jumlah" required min="1" value="1">
            </div>
            <div class="form-group">
                <label>Foto Referensi / Model</label>
                <input type="file" name="foto_referensi" accept="image/*">
                <small>Upload contoh model yang diinginkan (opsional)</small>
            </div>
            <div class="form-group">
                <label>Budget Estimasi (Rp)</label>
                <input type="number" name="budget_estimasi" placeholder="Contoh: 500000">
            </div>
            <div class="form-group">
                <label>Catatan Tambahan</label>
                <textarea name="catatan" rows="3" placeholder="Warna, bahan, ukuran, dll..."></textarea>
            </div>
            <button type="submit" class="btn" style="width: 100%;">📩 Kirim Pesanan</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>