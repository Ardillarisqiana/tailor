<?php include 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- BUNGKUS FORM PAKAI form-container biar GA GEDE -->
<div class="form-container">
    <div class="form-card">
        <h1 style="text-align: center;">📝 Daftar Jadi Penjahit</h1>
        <p style="text-align: center; margin-bottom: 25px; color: #666;">Isi form di bawah untuk punya portfolio sendiri</p>
       
        <?php
        // Proses kalau form di-submit
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_lengkap = $_POST['nama_lengkap'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            // no_hp sudah TIDAK DIPAKAI! hanya pakai whatsapp
            $nama_toko = $_POST['nama_toko'];
            $pengalaman_tahun = (int)$_POST['pengalaman_tahun'];
            $spesialisasi = $_POST['spesialisasi'];
            $alamat_lengkap = $_POST['alamat_lengkap'];
            $whatsapp = $_POST['whatsapp'];
            $instagram = $_POST['instagram'];
            $harga_minimal = (int)$_POST['harga_minimal'];
            
            // Upload foto profil
            $foto_profil = '';
            if(isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
                $target_dir = "uploads/";
                $foto_profil = time() . '_' . basename($_FILES['foto_profil']['name']);
                $target_file = $target_dir . $foto_profil;
                move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_file);
            }
            
            // PERHATIAN: no_hp TIDAK dimasukkan karena kolomnya sudah dihapus dari database
            $stmt = mysqli_prepare($conn, "INSERT INTO penjahit (nama_lengkap, username, email, password, nama_toko, pengalaman_tahun, spesialisasi, alamat_lengkap, whatsapp, instagram, harga_minimal, foto_profil) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssssissssis", $nama_lengkap, $username, $email, $password, $nama_toko, $pengalaman_tahun, $spesialisasi, $alamat_lengkap, $whatsapp, $instagram, $harga_minimal, $foto_profil);
            
            if(mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                // ✅ BERHASIL → LANGSUNG KE HALAMAN BERANDA
                echo '<div class="alert alert-success">✅ Pendaftaran berhasil! Mengalihkan ke beranda...</div>';
                echo '<meta http-equiv="refresh" content="2;url=index.php">';
            } else {
                $db_error = mysqli_error($conn);
                mysqli_stmt_close($stmt);
                // ❌ GAGAL → TETAP DI FORM, TAMPILIN ERROR
                echo '<div class="alert alert-error">❌ Error: ' . $db_error . '</div>';
            }
        }
        ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama_lengkap" required placeholder="Contoh: jelita">
            </div>
            
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" required placeholder="contoh: jelita_jahit">
                <small style="color: #888;">🔗 Buat link portfolio kamu nanti</small>
            </div>
            
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required placeholder="contoh: jelita@gmail.com">
            </div>
            
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter">
            </div>
            
            <div class="form-group">
                <label>📱 Nomor WhatsApp (untuk kontak) *</label>
                <input type="text" name="whatsapp" required placeholder="Contoh: 08..">
                <small style="color: #888;">✅ Pelanggan akan hubungi kamu lewat sini</small>
            </div>
            
            <div class="form-group">
                <label>Nama Toko/Bisnis</label>
                <input type="text" name="nama_toko" placeholder="Contoh: Jahit Fikri Collection">
            </div>
            
            <div class="form-group">
                <label>Pengalaman (tahun) *</label>
                <input type="number" name="pengalaman_tahun" required placeholder="0">
            </div>
            
            <div class="form-group">
                <label>Spesialisasi *</label>
                <select name="spesialisasi" required>
                    <option value="">Pilih spesialisasi</option>
                    <option value="Baju Pengantin">👰 Baju Pengantin</option>
                    <option value="Pakaian Sehari-hari">👕 Pakaian Sehari-hari</option>
                    <option value="Seragam">👔 Seragam</option>
                    <option value="Batik">🦋 Batik</option>
                    <option value="Busana Muslim">🧕 Busana Muslim</option>
                    <option value="Pakaian Anak">👶 Pakaian Anak</option>
                    <option value="Semua Jenis">✨ Semua Jenis</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>📍 Alamat Lengkap *</label>
                <textarea name="alamat_lengkap" rows="3" required placeholder="Contoh: Jl. Mawar No. 10, RT 01 RW 02, Kelurahan Melati, Kecamatan Flamboyan, Kota Surabaya, Jawa Timur"></textarea>
                <small style="color: #888;">Tulis alamat lengkap biar pelanggan tahu lokasi kamu</small>
            </div>
            
            <div class="form-group">
                <label>Instagram (opsional)</label>
                <input type="text" name="instagram" placeholder="@username">
            </div>
            
            <div class="form-group">
                <label>💰 Harga Minimal (Rp) *</label>
                <input type="number" name="harga_minimal" required placeholder="Contoh: 100000">
            </div>
            
            <div class="form-group">
                <label>📷 Foto Profil</label>
                <input type="file" name="foto_profil" accept="image/*">
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">✨ Daftar Sekarang ✨</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>