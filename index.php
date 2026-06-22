<?php include 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<div class="card" style="text-align: center;">
    <h1>✂️ Selamat Datang di JahitLink</h1>
    <p style="font-size: 18px; margin: 20px 0;">Tempat mencari penjahit profesional untuk kebutuhan pakaianmu</p>
    
    <?php if(!isset($_SESSION['user_id'])): ?>
        <a href="register.php" class="btn" style="margin-top: 20px;">📝 Daftar Jadi Penjahit</a>
    <?php else: ?>
        <a href="dashboard.php" class="btn">📋 Dashboard Saya</a>
    <?php endif; ?>
</div>

<div class="card">
    <h2>📋 Daftar Penjahit</h2>
    
    <?php
    // Ambil SEMUA penjahit
    $query = "SELECT * FROM penjahit";
    $result = mysqli_query($conn, $query);
    
    // Hitung jumlah data
    $jumlah_data = mysqli_num_rows($result);
    
    // Tampilkan jumlah data
    echo '<div class="alert alert-info" style="background: #e3f2fd;">📊 Total data penjahit di database: ' . $jumlah_data . ' orang</div>';
    
    if($jumlah_data > 0):
    ?>
        <div class="tailor-grid">
            <?php while($tailor = mysqli_fetch_assoc($result)): ?>
            <div class="tailor-card">
                <?php if($tailor['foto_profil'] && file_exists("uploads/" . $tailor['foto_profil'])): ?>
                    <img src="uploads/<?php echo $tailor['foto_profil']; ?>" alt="Foto">
                <?php else: ?>
                    <img src="https://via.placeholder.com/100x100?text=✂️" alt="No Photo">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($tailor['nama_toko'] ?: $tailor['nama_lengkap']); ?></h3>
                <p><strong>Status:</strong> 
                    <?php 
                    if($tailor['status'] == 'approved') {
                        echo '<span style="color: green;">✅ Disetujui</span>';
                    } elseif($tailor['status'] == 'pending') {
                        echo '<span style="color: orange;">⏳ Menunggu</span>';
                    } else {
                        echo '<span style="color: red;">❌ Ditolak</span>';
                    }
                    ?>
                </p>
                <p>📍 <?php echo htmlspecialchars(substr($tailor['alamat_lengkap'] ?: '-', 0, 50)); ?>...</p>
                <p>⭐ <?php echo $tailor['pengalaman_tahun']; ?> tahun</p>
                
                <!-- RATING AMAN (tanpa error) -->
                <p>
                    <?php 
                    $rating_total = isset($tailor['rating_total']) ? $tailor['rating_total'] : 0;
                    $jumlah_rating = isset($tailor['jumlah_rating']) ? $tailor['jumlah_rating'] : 0;
                    $rating_show = $jumlah_rating > 0 ? round($rating_total / $jumlah_rating, 1) : 0;
                    
                    if($rating_show > 0) {
                        for($i=1; $i<=floor($rating_show); $i++) echo '⭐';
                        echo ' ' . $rating_show;
                    } else {
                        echo '⭐ Belum ada rating';
                    }
                    ?>
                </p>
                
                <p>🎯 <?php echo htmlspecialchars($tailor['spesialisasi']); ?></p>
                <p>💰 Rp <?php echo number_format($tailor['harga_minimal'], 0, ',', '.'); ?>+</p>
                
                <?php if($tailor['status'] == 'approved'): ?>
                    <a href="portofolio.php?id=<?php echo $tailor['id']; ?>" class="btn" style="margin-top: 15px; display: inline-block; padding: 8px 20px; font-size: 14px;">Lihat Portofolio</a>
                <?php else: ?>
                    <button class="btn" style="margin-top: 15px; background: #ccc; cursor: not-allowed;" disabled>Menunggu Verifikasi</button>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info" style="text-align: center;">
            ⏳ Belum ada data penjahit.<br>
            <a href="register.php" style="color: #2a5298;">Klik di sini untuk daftar jadi penjahit</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>