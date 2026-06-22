<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JahitLink - Portfolio Penjahit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
     <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">✂️ JahitLink</a>
            <div class="nav-links">
                <a href="index.php">Beranda</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                <?php elseif(isset($_SESSION['admin_login'])): ?>
                    <a href="admin.php">Admin Panel</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                    <a href="register.php" class="btn-register">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="container">
</body>
</html>
   

