# e-Rapor Sisipan – SMKN 10 Surabaya

Sistem administrasi akademik berbasis PHP Native + MySQL (PDO) dengan arsitektur MVC Manual.

---

## 📦 Instalasi

### 1. Copy ke XAMPP

```
Pastikan folder sudah ada di: D:\xampp\htdocs\e-rapor10\
```

### 2. Buat Database

**Cara A – phpMyAdmin:**
1. Buka `http://localhost/phpmyadmin`
2. Klik **Import**
3. Upload file `database/schema.sql`

**Cara B – Command Line:**
```bash
mysql -u root -p < D:\xampp\htdocs\e-rapor10\database\schema.sql
```

### 3. Konfigurasi Database

Edit file `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'erapor10');
define('DB_USER', 'root');
define('DB_PASS', '');  // sesuaikan password MySQL Anda
```

### 4. Generate Password Hash

Buka browser: `http://localhost/e-rapor10/database/genhash.php`

Salin SQL UPDATE yang dihasilkan dan jalankan di phpMyAdmin.

**Lalu hapus file `database/genhash.php`!**

### 5. Akses Sistem

```
http://localhost/e-rapor10/
```

---

## 👤 Akun Sample

| Email | Password | Role |
|-------|----------|------|
| admin@smkn10sby.sch.id | Password123 | Admin |
| budi@smkn10sby.sch.id  | Password123 | Guru Mapel + Wali Kelas |
| dewi@smkn10sby.sch.id  | Password123 | Guru Mapel |
| sari@smkn10sby.sch.id  | Password123 | Guru Mapel |
| ahmad@smkn10sby.sch.id | Password123 | Kaprogli |
| rina@smkn10sby.sch.id  | Password123 | Kaprogli |
| dian@smkn10sby.sch.id  | Password123 | Wali Kelas |
| hendra@smkn10sby.sch.id| Password123 | Wali Kelas |

---

## 🏗️ Struktur Folder (Organized)

```
e-rapor10/
├── app/                 # Inti Aplikasi (Logic)
│   ├── Controllers/     # Logic HTTP (Menerima request, memanggil Model, render View)
│   │   ├── AuthController.php      # Login & Logout
│   │   ├── DashboardController.php # Beranda Utama
│   │   ├── UserController.php      # Manajemen User (Split)
│   │   ├── AcademicController.php  # Data Sekolah (Split)
│   │   ├── AdminController.php     # Fitur Sistem (Kenaikan Kelas/Reset)
│   │   └── NilaiController.php     # Input Nilai & Monitoring
│   ├── Models/          # Database Interaction (Query SQL)
│   │   ├── UserModel.php
│   │   ├── KelasModel.php
│   │   └── ...
│   └── Core/            # Library Inti / Engine
│       ├── Session.php      # Pengaturan Sesi
│       ├── Middleware.php   # Keamanan & Hak Akses
│       └── helpers.php      # Fungsi bantuan global (e, redirect, asset)
├── config/              # Pengaturan (Database, Nama App)
├── public/              # File Publik (CSS, JS, Gambar, Template)
├── views/               # Tampilan (HTML/PHP)
├── index.php            # Gerbang Utama (Front Controller)
└── routes.php           # Pengatur Lalu Lintas URL
```

---

## 💡 Tips untuk Developer Awal

1.  **MVC Sederhana**: Project ini menggunakan konsep *Model-View-Controller*.
    *   Ingin ubah tampilan? Cek folder `views/`.
    *   Ingin ubah query database? Cek folder `app/Models/`.
    *   Ingin ubah alur logika? Cek folder `app/Controllers/`.
2.  **Helpers**: Gunakan fungsi `e($string)` untuk menampilkan data agar aman dari XSS. Gunakan `asset('path')` untuk memanggil file di folder public.
3.  **Routing**: Jika Anda menambah halaman baru, daftarkan URL-nya di `routes.php`.

---

## 🔐 Fitur Keamanan

- ✅ **SQL Injection Prevention**: Menggunakan PDO Prepared Statements.
- ✅ **Password Security**: Menggunakan `password_hash()` BCRYPT.
- ✅ **CSRF Protection**: Token otomatis pada setiap form POST.
- ✅ **XSS Protection**: Fungsi `e()` untuk escaping output.
- ✅ **Role-Based Access**: Middleware untuk membatasi akses Admin/Guru/Wali/Kaprogli.

---

## ⚙️ URL Parameter & Router

Sistem menggunakan `?page=xxx` sebagai router:

| Halaman | Deskripsi |
|---------|-----------|
| `?page=dashboard` | Halaman utama setelah login |
| `?page=admin.users` | Kelola akun pengguna |
| `?page=admin.kelas` | Kelola data kelas & wali |
| `?page=nilai` | Input nilai oleh Guru |
| `?page=monitoring` | Cetak rapor oleh Wali Kelas |
