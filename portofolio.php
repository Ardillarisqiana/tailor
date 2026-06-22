<?php 
include 'includes/config.php'; 
include 'includes/header.php'; 
?>

<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT * FROM penjahit WHERE id = $id";
$result = mysqli_query($conn, $query);
$tailor = mysqli_fetch_assoc($result);

if(!$tailor) {
    echo '<div class="card" style="text-align: center;">';
    echo '<h1>⚠️ Penjahit tidak ditemukan</h1>';
    echo '<a href="index.php" class="btn">Kembali ke Beranda</a>';
    echo '</div>';
    include 'includes/footer.php';
    exit();
}
?>

<div class="card" style="padding: 40px;">
    <!-- PROFIL PENJAHIT -->
    <div style="text-align: center; margin-bottom: 50px;">
        <?php 
        $foto_profil = !empty($tailor['foto_profil']) && file_exists("uploads/" . $tailor['foto_profil']) 
                    ? "uploads/" . $tailor['foto_profil'] 
                    : "https://via.placeholder.com/180x180?text=✂️";
        ?>
        <img src="<?php echo $foto_profil; ?>" style="width: 180px; height: 180px; border-radius: 50%; object-fit: cover; border: 4px solid #2a5298; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
        
        <h2 style="margin-top: 20px; font-size: 28px;"><?php echo htmlspecialchars($tailor['nama_toko'] ?: $tailor['nama_lengkap']); ?></h2>
        
        <div style="margin: 20px 0;">
            <span style="background: #f0f0f0; padding: 8px 16px; border-radius: 30px; margin: 5px; display: inline-block; font-size: 14px;">⭐ <?php echo $tailor['pengalaman_tahun']; ?> tahun</span>
            <span style="background: #f0f0f0; padding: 8px 16px; border-radius: 30px; margin: 5px; display: inline-block; font-size: 14px;">🎯 <?php echo htmlspecialchars($tailor['spesialisasi']); ?></span>
            <span style="background: #f0f0f0; padding: 8px 16px; border-radius: 30px; margin: 5px; display: inline-block; font-size: 14px;">💰 Rp <?php echo number_format($tailor['harga_minimal'], 0, ',', '.'); ?>+</span>
        </div>
        
        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $tailor['whatsapp']); ?>" target="_blank" style="display: inline-block; background: #25D366; color: white; padding: 12px 30px; border-radius: 50px; text-decoration: none; margin: 15px 0; font-weight: bold; font-size: 16px;">
            💬 Hubungi WhatsApp
        </a>

        <a href="order_form.php?id=<?php echo $tailor['id']; ?>" class="btn" style="background: #ff9800; margin-top: 10px; display: inline-block;">
            📝 Pesan Sekarang
        </a>
        
        <!-- ALAMAT LEBAR -->
        <div style="margin-top: 30px; text-align: left; background: #e8f0fe; padding: 25px 30px; border-radius: 15px; width: 100%; border-left: 5px solid #2a5298;">
            <strong style="font-size: 16px; display: block; margin-bottom: 10px;">📍 Alamat Lengkap:</strong>
            <p style="margin-top: 5px; font-size: 14px; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($tailor['alamat_lengkap'] ?: '-')); ?></p>
        </div>
    </div>

    <!-- DAFTAR PORTFOLIO KARYA - LEBAR -->
    <h3 style="margin-bottom: 30px; font-size: 28px; text-align: center;">📸 Portfolio Karya</h3>
    
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px;">
        <?php
        $porto_query = "SELECT * FROM portfolio WHERE penjahit_id = {$tailor['id']} ORDER BY dibuat_pada DESC";
        $porto_result = mysqli_query($conn, $porto_query);
        
        if(mysqli_num_rows($porto_result) > 0) {
            while($item = mysqli_fetch_assoc($porto_result)) {
                $foto_item = !empty($item['foto']) && file_exists("uploads/" . $item['foto']) 
                            ? "uploads/" . $item['foto'] 
                            : "https://via.placeholder.com/400x250?text=Tidak+Ada+Foto";
                ?>
                <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s;">
                    <img src="<?php echo $foto_item; ?>" style="width: 100%; height: 220px; object-fit: cover;">
                    <div style="padding: 18px;">
                        <h4 style="margin-bottom: 8px; font-size: 16px;"><?php echo htmlspecialchars($item['judul']); ?></h4>
                        <span style="background: #e3f2fd; padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; margin-bottom: 10px;">📁 <?php echo htmlspecialchars($item['kategori']); ?></span>
                        <p style="font-size: 13px; color: #666; line-height: 1.5;"><?php echo substr(htmlspecialchars($item['deskripsi']), 0, 80); ?>...</p>
                        <p style="margin-top: 12px; font-size: 16px; font-weight: bold; color: #2a5298;">💰 Rp <?php echo number_format($item['harga_estimasi'], 0, ',', '.'); ?></p>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div style="grid-column: 1/-1; text-align: center; padding: 60px; background: #f9f9f9; border-radius: 16px;">';
            echo '<p style="font-size: 18px; color: #888;">📭 Belum ada portfolio.</p>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<!-- ===== BAGIAN RATING - TAMBAHAN DI BAWAH ===== -->
<div class="card" style="margin-top: 30px;">
    <h3>⭐ Rating & Review</h3>
    
    <?php
    // Hitung rata-rata rating
    $rating_query = "SELECT AVG(rating) as rata, COUNT(*) as total FROM rating WHERE penjahit_id = {$tailor['id']}";
    $rating_result = mysqli_query($conn, $rating_query);
    $rating_data = mysqli_fetch_assoc($rating_result);
    $rata_rating = round($rating_data['rata'], 1);
    $total_rating = $rating_data['total'];
    ?>
    
    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px; flex-wrap: wrap;">
        <div style="text-align: center;">
            <div style="font-size: 48px; font-weight: bold; color: #f39c12;"><?php echo $rata_rating ?: 0; ?></div>
            <div>⭐ dari 5</div>
            <div style="font-size: 14px; color: #666;">(<?php echo $total_rating; ?> ulasan)</div>
        </div>
        <div style="flex: 1;">
            <?php
            // Tampilkan bintang
            $bintang_penuh = floor($rata_rating);
            $bintang_kosong = 5 - $bintang_penuh;
            echo '<div style="font-size: 24px;">';
            for($i = 1; $i <= $bintang_penuh; $i++) echo '⭐';
            for($i = 1; $i <= $bintang_kosong; $i++) echo '☆';
            echo '</div>';
            ?>
        </div>
    </div>
    
    <!-- FORM KASIH RATING -->
    <div style="background: #f9f9f9; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
        <h4>💬 Kasih Rating & Review</h4>
        <form method="POST" action="">
            <input type="hidden" name="aksi_rating" value="submit">
            <div class="form-group">
                <label>Nama Anda</label>
                <input type="text" name="nama_pelanggan" required placeholder="Contoh: Budi">
            </div>
            <div class="form-group">
                <label>Rating</label>
                <select name="rating" required style="width: auto;">
                    <option value="5">⭐⭐⭐⭐⭐ - Sangat Puas</option>
                    <option value="4">⭐⭐⭐⭐ - Puas</option>
                    <option value="3">⭐⭐⭐ - Biasa Saja</option>
                    <option value="2">⭐⭐ - Kurang Puas</option>
                    <option value="1">⭐ - Tidak Puas</option>
                </select>
            </div>
            <div class="form-group">
                <label>Review (opsional)</label>
                <textarea name="review" rows="3" placeholder="Ceritakan pengalaman Anda..."></textarea>
            </div>
            <button type="submit" class="btn">Kirim Rating</button>
        </form>
    </div>
    
    <!-- DAFTAR REVIEW -->
    <div style="margin-top: 20px;">
        <?php
        // Proses simpan rating
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi_rating'])) {
            $nama = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);
            $rating = (int)$_POST['rating'];
            $review = mysqli_real_escape_string($conn, $_POST['review']);
            
            $insert = "INSERT INTO rating (penjahit_id, nama_pelanggan, rating, review) 
                       VALUES ({$tailor['id']}, '$nama', $rating, '$review')";
            if(mysqli_query($conn, $insert)) {
                // Update total rating di tabel penjahit
                $update_total = "UPDATE penjahit SET 
                                rating_total = (SELECT SUM(rating) FROM rating WHERE penjahit_id = {$tailor['id']}),
                                jumlah_rating = (SELECT COUNT(*) FROM rating WHERE penjahit_id = {$tailor['id']})
                                WHERE id = {$tailor['id']}";
                mysqli_query($conn, $update_total);
                echo '<div class="alert alert-success">✅ Terima kasih atas ratingnya!</div>';
                echo '<meta http-equiv="refresh" content="0">';
            } else {
                echo '<div class="alert alert-error">❌ Gagal: ' . mysqli_error($conn) . '</div>';
            }
        }
        
        // Tampilkan semua review
        $review_query = "SELECT * FROM rating WHERE penjahit_id = {$tailor['id']} ORDER BY created_at DESC";
        $review_result = mysqli_query($conn, $review_query);
        
        if(mysqli_num_rows($review_result) > 0):
            while($review = mysqli_fetch_assoc($review_result)):
        ?>
        <div style="border-bottom: 1px solid #eee; padding: 15px 0;">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                <div>
                    <strong><?php echo htmlspecialchars($review['nama_pelanggan']); ?></strong>
                    <span style="color: #f39c12; margin-left: 10px;">
                        <?php for($i=1; $i<=$review['rating']; $i++) echo '⭐'; ?>
                    </span>
                </div>
                <small style="color: #999;"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></small>
            </div>
            <?php if($review['review']): ?>
                <p style="margin-top: 8px; color: #555;"><?php echo nl2br(htmlspecialchars($review['review'])); ?></p>
            <?php endif; ?>
        </div>
        <?php 
            endwhile;
        else:
            echo '<p style="text-align: center; color: #999; padding: 20px;">Belum ada rating. Jadilah yang pertama!</p>';
        endif;
        ?>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>