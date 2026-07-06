<b>Nama : Ardilla Risqiana</b>
<hr>
<b>NIM  : 101230088</b>
<hr>
<b>Kelas: TF23A</b>
<hr>
# JahitLink - Marketplace Penjahit Online

**JahitLink** adalah aplikasi berbasis web yang menghubungkan klien dengan penjahit profesional. Klien dapat mencari, melihat portofolio, dan memesan jahitan secara online, sementara penjahit dapat mengelola toko, portofolio, dan pesanan melalui dashboard khusus. Admin bertugas memverifikasi dan menyetujui pendaftaran penjahit.

## Fitur Utama

- **Registrasi & Profil Penjahit** – Penjahit dapat mendaftar, mengisi profil, dan mengelola toko
- **Verifikasi Admin** – Admin menyetujui atau menolak pendaftaran penjahit
- **Portofolio** – Penjahit dapat mengunggah dan mengelola karya jahitan
- **Pemesanan Online** – Klien dapat memesan jahitan dengan sistem antrean (maks. 8 pesanan aktif per bulan)
- **Manajemen Pesanan** – Penjahit dapat menerima, menolak, atau menyelesaikan pesanan
- **Rating & Ulasan** – Klien dapat memberikan rating dan ulasan setelah pesanan selesai
- **Integrasi WhatsApp** – Menampilkan nomor WhatsApp penjahit untuk komunikasi langsung
- **Halaman Publik** – Setiap penjahit memiliki halaman profil & portofolio yang bisa diakses publik

## Teknologi yang Digunakan

| Teknologi | Keterangan |
|-----------|------------|
| **PHP** | Bahasa pemrograman backend (native, tanpa framework) |
| **MySQL** | Database management system |
| **HTML5 & CSS3** | Tampilan antarmuka pengguna |
| **JavaScript** | Interaktivitas frontend |
| **XAMPP** | Local development environment (Apache + MySQL + PHP) |
| **Python** | Digunakan untuk AGENT.py (tools pendukung pengembangan) |
| **Git & GitHub** | Version control & repository hosting |

## Persyaratan Sistem

- **XAMPP** versi 7.4+ (PHP 7.4+ recommended) atau web server dengan dukungan PHP
- **MySQL** / **MariaDB**
- **Web browser** modern (Chrome, Firefox, Edge)
- **Git** (opsional, untuk cloning repository)

## Cara Instalasi

1. **Clone repositori**
   ```bash
   git clone https://github.com/Ardillarisqiana/tailor.git
   ```

2. **Pindahkan folder ke direktori XAMPP**
   - Salin folder `tailor` ke `C:\xampp\htdocs\`

3. **Buat database**
   - Buka **phpMyAdmin** (`http://localhost/phpmyadmin`)
   - Buat database baru dengan nama **`db_jahitlink`**
   - Import file SQL (jika tersedia) atau buat tabel sesuai skema berikut:

   **Tabel: `penjahit`**
   ```sql
   CREATE TABLE penjahit (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nama_lengkap VARCHAR(255),
       username VARCHAR(100),
       email VARCHAR(255),
       password VARCHAR(255),
       nama_toko VARCHAR(255),
       pengalaman_tahun INT,
       spesialisasi VARCHAR(255),
       alamat_lengkap TEXT,
       whatsapp VARCHAR(20),
       instagram VARCHAR(255),
       harga_minimal DECIMAL(10,0),
       foto_profil VARCHAR(255),
       status ENUM('pending','approved','rejected') DEFAULT 'pending',
       rating_total DECIMAL(3,1) DEFAULT 0,
       jumlah_rating INT DEFAULT 0,
       dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

   **Tabel: `portfolio`**
   ```sql
   CREATE TABLE portfolio (
       id INT AUTO_INCREMENT PRIMARY KEY,
       penjahit_id INT,
       judul VARCHAR(255),
       deskripsi TEXT,
       kategori VARCHAR(255),
       foto VARCHAR(255),
       harga_estimasi DECIMAL(10,0),
       dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (penjahit_id) REFERENCES penjahit(id) ON DELETE CASCADE
   );
   ```

   **Tabel: `orders`**
   ```sql
   CREATE TABLE orders (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nomor_order VARCHAR(20),
       penjahit_id INT,
       nama_klien VARCHAR(255),
       no_hp VARCHAR(20),
       jenis_pakaian VARCHAR(255),
       jumlah INT,
       foto_referensi VARCHAR(255),
       budget_estimasi DECIMAL(10,0),
       catatan TEXT,
       status ENUM('pending','diterima','ditolak','selesai') DEFAULT 'pending',
       FOREIGN KEY (penjahit_id) REFERENCES penjahit(id) ON DELETE CASCADE
   );
   ```

   **Tabel: `rating`**
   ```sql
   CREATE TABLE rating (
       id INT AUTO_INCREMENT PRIMARY KEY,
       penjahit_id INT,
       nama_pelanggan VARCHAR(255),
       rating INT,
       review TEXT,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (penjahit_id) REFERENCES penjahit(id) ON DELETE CASCADE
   );
   ```

   **Tabel: `order_sequence`**
   ```sql
   CREATE TABLE order_sequence (
       last_number INT DEFAULT 0
   );
   INSERT INTO order_sequence (last_number) VALUES (0);
   ```

4. **Konfigurasi database**
   - Buka file `includes/config.php`
   - Sesuaikan kredensial jika diperlukan (default: `root` / password kosong)

5. **Akses aplikasi**
   - Buka browser dan akses `http://localhost/tailor`

## Cara Menjalankan Aplikasi menggunakan XAMPP

1. **Pastikan XAMPP terinstall** dan service **Apache** serta **MySQL** sudah berjalan (gunakan XAMPP Control Panel)

2. **Letakkan project** di folder `C:\xampp\htdocs\tailor`

3. **Buat database** `db_jahitlink` dan tabel-tabel di atas melalui phpMyAdmin

4. **Buka browser** dan akses:
   ```
   http://localhost/tailor
   ```

5. **Halaman utama** akan menampilkan daftar penjahit yang sudah disetujui

## Struktur Folder

```
tailor/
├── index.php                  # Halaman utama (daftar penjahit)
├── login.php                  # Halaman login (admin & penjahit)
├── register.php               # Halaman registrasi penjahit
├── admin.php                  # Panel admin (verifikasi penjahit)
├── dashboard.php              # Dashboard penjahit (portofolio & pesanan)
├── portofolio.php             # Halaman publik portofolio penjahit
├── order_form.php             # Form pemesanan jahitan
├── hapus_portofolio.php       # Hapus item portofolio
├── logout.php                 # Logout & destroy session
├── style.css                  # Stylesheet utama
├── AGENT.py                   # Python AI agent (tools pengembangan)
├── includes/
│   ├── config.php             # Konfigurasi database & session
│   ├── auth.php               # Middleware autentikasi
│   ├── header.php             # Header & navigasi
│   └── footer.php             # Footer
└── uploads/                   # Folder unggahan (foto profil & portofolio)
```

## Akun Login Default

### Admin
| Field    | Value        |
|----------|--------------|
| **Email** | `admin`      |
| **Password** | `admin123` |

> Login admin dapat dilakukan melalui halaman login (`login.php`) dengan memasukkan email `admin` dan password `admin123`.

### Penjahit
| Field    | Keterangan                           |
|----------|--------------------------------------|
| **Registrasi** | Daftar melalui `register.php`   |
| **Status** | Menunggu persetujuan admin         |

> Akun penjahit harus dibuat melalui formulir pendaftaran dan disetujui oleh admin sebelum bisa login.

## Screenshot

> *(Tambahkan screenshot aplikasi di sini)*

| Halaman | Screenshot |
|---------|------------|
| Halaman Utama | ![]() |
| Login | ![]() |
| Dashboard Penjahit | ![]() |
| Panel Admin | ![]() |
| Portofolio Publik | ![]() |
| Form Pemesanan | ![]() |

## Kontributor

- **Ardillarisqiana** – [GitHub](https://github.com/Ardillarisqiana)

## Lisensi

Proyek ini dilisensikan di bawah **MIT License** – lihat file `LICENSE` untuk detail lebih lanjut.
