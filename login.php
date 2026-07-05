<?php 
include 'includes/config.php';

// Kalau sudah login, langsung ke dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
if(isset($_SESSION['admin_login'])) {
    header("Location: admin.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="form-container">
    <div class="form-card">
        <h1 style="text-align: center;">🔐 Login</h1>
        <p style="text-align: center; margin-bottom: 25px; color: #666;">Masuk ke akun Anda</p>
        
        <?php
        // Proses login
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            // CEK APAKAH ADMIN?
            $admin_password = "admin123"; // Ganti dengan password admin Anda
            
            if($email == 'admin' && $password == $admin_password) {
                // LOGIN SEBAGAI ADMIN
                $_SESSION['admin_login'] = true;
                echo '<div class="alert alert-success">✅ Login sebagai Admin! Mengalihkan...</div>';
                echo '<meta http-equiv="refresh" content="1;url=admin.php">';
            } else {
                // CEK SEBAGAI USER PENJAHIT
                $stmt = mysqli_prepare($conn, "SELECT * FROM penjahit WHERE email = ?");
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if(mysqli_num_rows($result) == 1) {
                    $user = mysqli_fetch_assoc($result);
                    
                    if(password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        
                        echo '<div class="alert alert-success">✅ Login berhasil! Mengalihkan ke dashboard...</div>';
                        echo '<meta http-equiv="refresh" content="1;url=dashboard.php">';
                    } else {
                        echo '<div class="alert alert-error">❌ Password salah!</div>';
                    }
                } else {
                    echo '<div class="alert alert-error">❌ Email tidak ditemukan!</div>';
                }
                
                mysqli_stmt_close($stmt);
            }
        }
        ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" required placeholder="masukkan email">
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="masukkan password">
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">🔓 Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            Belum punya akun? <a href="register.php" style="color: #2a5298;">Daftar jadi penjahit</a>
        </p>
        
        <hr style="margin: 20px 0;">
        
        <!-- <p style="text-align: center; font-size: 12px; color: #888;">
            Admin: masukkan email = <strong>admin</strong> dengan password = <strong>admin123</strong>
        </p> -->
    </div>
</div>

<?php include 'includes/footer.php'; ?>