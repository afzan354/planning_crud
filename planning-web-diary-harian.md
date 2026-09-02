# Planning Project: Web Diary Harian (dengan User Login)

> Project lanjutan untuk kelas 11 RPL — mata pelajaran Pemrograman Web
> Materi: PHP Native + Bootstrap + Session/Login (lanjutan dari CRUD dasar Inventaris Toko)
> Fokus: setiap user punya diary masing-masing setelah login (data antar user terpisah)

---

## 1. Deskripsi Project

Aplikasi diary harian pribadi berbasis web, dengan fitur:

- Register akun baru
- Login / Logout (pakai Session)
- Setiap user hanya bisa melihat & mengelola diary miliknya sendiri
- CRUD diary: Tambah, Lihat, Edit, Hapus catatan harian
- Setiap catatan punya: judul, isi, tanggal

Konsep baru dibanding project sebelumnya:
- **Autentikasi** (login/register)
- **Session** (menyimpan siapa yang sedang login)
- **Relasi antar tabel** (`users` ↔ `diary`, 1 user punya banyak diary)

---

## 2. Struktur Folder Project

```
web-diary/
│
├── config.php         # Koneksi ke database
├── register.php       # Form + proses pendaftaran user baru
├── login.php          # Form + proses login, membuat session
├── logout.php         # Menghapus session (proses logout)
├── index.php          # Dashboard, menampilkan semua diary milik user yang login
├── tambah.php          # Form + proses tambah diary baru
├── edit.php            # Form + proses edit diary
├── hapus.php           # Proses hapus diary
└── cek_login.php       # (opsional) file include untuk cek apakah user sudah login
```

**Fungsi tiap file (ringkas):**

| File            | Fungsi                                                                 |
|------------------|-------------------------------------------------------------------------|
| `config.php`     | Menyambungkan PHP ke database MySQL                                    |
| `register.php`   | Menampilkan form daftar akun & menyimpan user baru ke tabel `users`     |
| `login.php`      | Menampilkan form login, mencocokkan username/password, membuat session |
| `logout.php`     | Menghapus/menghancurkan session, kembali ke halaman login              |
| `index.php`      | Halaman utama setelah login, menampilkan semua diary milik user tsb    |
| `tambah.php`     | Form + proses menyimpan catatan diary baru, dikaitkan ke `user_id`      |
| `edit.php`       | Form + proses mengubah isi catatan diary yang sudah ada                |
| `hapus.php`      | Menghapus 1 catatan diary berdasarkan id                               |
| `cek_login.php`  | Dicek/di-*include* di setiap halaman agar tidak bisa diakses tanpa login |

---

## 3. Struktur Database (Perintah SQL Lengkap)

```sql
-- 1. Buat database
CREATE DATABASE db_diary;
USE db_diary;

-- 2. Tabel users (menyimpan akun)
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Tabel diary (menyimpan catatan harian, relasi ke users)
CREATE TABLE diary (
    id_diary INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    isi TEXT NOT NULL,
    tanggal DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE
);

-- 4. (Opsional) Contoh data awal
INSERT INTO users (username, password, nama_lengkap) VALUES
('budi123', 'akan_diisi_hash_password_disini', 'Budi Santoso');
```

**Penjelasan struktur:**
- Tabel `users` menyimpan akun. Kolom `password` bertipe `VARCHAR(255)` karena akan diisi hasil enkripsi `password_hash()` (panjang karakternya jauh lebih panjang dari password asli).
- Tabel `diary` punya kolom `user_id` sebagai *foreign key* yang merujuk ke `users.id_user` — ini yang membuat setiap catatan "terikat" ke pemiliknya.
- `ON DELETE CASCADE` artinya jika 1 user dihapus, semua diary miliknya otomatis ikut terhapus.

Referensi: [PHP MySQL Create Table – W3Schools](https://www.w3schools.com/php/php_mysql_create_table.asp)

---

## 4. Frontend Bootstrap (Tampilan Setiap Halaman)

### 4.1 `register.php` — Form Daftar Akun

```html
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun - Web Diary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh">
    <div class="card p-4 shadow-sm" style="width:400px">
        <h3 class="mb-3 text-center">Daftar Akun</h3>
        <form method="POST">
            <div class="mb-3">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="daftar" class="btn btn-primary w-100">Daftar</button>
            <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="login.php">Login</a></p>
        </form>
    </div>
</div>
</body>
</html>
```

**Fungsi backend yang perlu dijelaskan ke siswa (tanpa kode penuh):**
`register.php` menerima input dari form, lalu mengenkripsi password dengan `password_hash()` sebelum disimpan ke tabel `users` lewat `INSERT INTO`.

Referensi:
- [PHP password_hash() – W3Schools](https://www.w3schools.com/php/func_password_hash.asp)
- [PHP MySQL Insert Data – W3Schools](https://www.w3schools.com/php/php_mysql_insert.asp)

---

### 4.2 `login.php` — Form Login

```html
<!DOCTYPE html>
<html>
<head>
    <title>Login - Web Diary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh">
    <div class="card p-4 shadow-sm" style="width:400px">
        <h3 class="mb-3 text-center">Login Diary</h3>

        <!-- Tampilkan pesan error di sini jika login gagal -->
        <div class="alert alert-danger d-none" id="pesanError">Username atau password salah</div>

        <form method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            <p class="text-center mt-3 mb-0">Belum punya akun? <a href="register.php">Daftar</a></p>
        </form>
    </div>
</div>
</body>
</html>
```

**Fungsi backend yang perlu dijelaskan ke siswa:**
`login.php` mencocokkan username dari form dengan data di tabel `users`, lalu memverifikasi password dengan `password_verify()`. Jika cocok, data user disimpan ke `$_SESSION` (misalnya `$_SESSION['user_id']`) sebagai tanda "sedang login", lalu redirect ke `index.php`.

Referensi:
- [PHP password_verify() – W3Schools](https://www.w3schools.com/php/func_password_verify.asp)
- [PHP Sessions – W3Schools](https://www.w3schools.com/php/php_sessions.asp)

---

### 4.3 `index.php` — Dashboard Diary

```html
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Diary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-primary px-3">
    <span class="navbar-brand">📔 Web Diary Harian</span>
    <div>
        <span class="text-white me-3">Halo, Nama User</span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <a href="tambah.php" class="btn btn-success mb-3">+ Tulis Diary Baru</a>

    <div class="row">
        <!-- Ulangi <div class="col-md-4"> ini untuk setiap catatan diary -->
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Judul Diary</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Tanggal</h6>
                    <p class="card-text">Cuplikan isi diary di sini...</p>
                    <a href="edit.php?id=1" class="btn btn-warning btn-sm">Edit</a>
                    <a href="hapus.php?id=1" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus catatan ini?')">Hapus</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
```

**Fungsi backend yang perlu dijelaskan ke siswa:**
Di paling atas `index.php` harus ada pengecekan session (`if (!isset($_SESSION['user_id'])) { redirect ke login.php }`), supaya halaman ini tidak bisa diakses tanpa login. Setelah itu, query `SELECT * FROM diary WHERE user_id = $_SESSION['user_id']` mengambil diary **milik user itu saja**, lalu di-*loop* untuk mengisi kartu Bootstrap di atas (bagian nama & judul diisi otomatis dari data, bukan teks statis seperti contoh).

Referensi:
- [PHP Sessions – W3Schools](https://www.w3schools.com/php/php_sessions.asp)
- [PHP MySQL Select Data – W3Schools](https://www.w3schools.com/php/php_mysql_select.asp)

---

### 4.4 `tambah.php` — Form Tulis Diary Baru

```html
<!DOCTYPE html>
<html>
<head>
    <title>Tulis Diary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container" style="max-width:600px">
    <h3>Tulis Diary Baru</h3>
    <form method="POST">
        <div class="mb-3">
            <label>Judul</label>
            <input type="text" name="judul" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Isi Diary</label>
            <textarea name="isi" class="form-control" rows="6" required></textarea>
        </div>
        <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
```

**Fungsi backend yang perlu dijelaskan ke siswa:**
Sama seperti `tambah.php` di project Inventaris sebelumnya (`INSERT INTO diary`), tapi ada 1 tambahan penting: kolom `user_id` diisi dari `$_SESSION['user_id']`, bukan dari input form — supaya diary otomatis tersimpan sebagai milik user yang sedang login.

Referensi: [PHP MySQL Insert Data – W3Schools](https://www.w3schools.com/php/php_mysql_insert.asp)

---

### 4.5 `edit.php` — Form Edit Diary

```html
<!DOCTYPE html>
<html>
<head>
    <title>Edit Diary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container" style="max-width:600px">
    <h3>Edit Diary</h3>
    <form method="POST">
        <div class="mb-3">
            <label>Judul</label>
            <input type="text" name="judul" class="form-control" value="Judul Lama" required>
        </div>
        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="2025-01-01" required>
        </div>
        <div class="mb-3">
            <label>Isi Diary</label>
            <textarea name="isi" class="form-control" rows="6" required>Isi diary lama di sini...</textarea>
        </div>
        <button type="submit" name="update" class="btn btn-warning">Update</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
```

**Fungsi backend yang perlu dijelaskan ke siswa:**
Mengambil 1 data diary berdasarkan `id_diary` dari URL (`SELECT ... WHERE id_diary=`), lalu menampilkannya sebagai nilai awal (`value="..."` dan isi `<textarea>`). Saat disimpan, gunakan `UPDATE diary SET ... WHERE id_diary= AND user_id=` — tambahan `AND user_id=` penting supaya user hanya bisa mengedit diary miliknya sendiri.

Referensi: [PHP MySQL Update Data – W3Schools](https://www.w3schools.com/php/php_mysql_update.asp)

---

### 4.6 `hapus.php` & `logout.php`

Tidak butuh tampilan (halaman ini bekerja di belakang layar):

- `hapus.php` → menjalankan `DELETE FROM diary WHERE id_diary= AND user_id=`, lalu redirect ke `index.php`.
- `logout.php` → memanggil `session_destroy()` untuk mengakhiri sesi login, lalu redirect ke `login.php`.

Referensi:
- [PHP MySQL Delete Data – W3Schools](https://www.w3schools.com/php/php_mysql_delete.asp)
- [PHP session_destroy() – W3Schools](https://www.w3schools.com/php/func_session_destroy.asp)

---

## 5. Alur Aplikasi (Flow)

```
register.php  →  login.php  →  index.php (dashboard)
                                    │
                    ┌───────────────┼───────────────┐
                    ▼               ▼               ▼
               tambah.php       edit.php        hapus.php
                    └───────────────┴───────────────┘
                                    ▼
                              index.php (refresh)
```

Setiap halaman (kecuali `login.php` & `register.php`) sebaiknya diawali dengan pengecekan session — bisa dijelaskan ke siswa sebagai konsep "halaman terkunci, hanya bisa dibuka kalau sudah login".

---

## 6. Konsep Keamanan Dasar yang WAJIB Ada di Level Ini

Berbeda dari project CRUD sebelumnya, karena project ini melibatkan **password & akun user**, ada 2 hal dasar yang sebaiknya tetap diajarkan meskipun materi keamanan lanjutan belum dibahas:

- Password **wajib** disimpan pakai `password_hash()`, jangan disimpan sebagai teks biasa.
- Setiap query `WHERE` yang berkaitan dengan data user (edit/hapus) sebaiknya ditambahkan `AND user_id=...`, supaya user A tidak bisa mengedit/menghapus data user B.

Filter XSS dan SQL Injection secara umum tetap belum diterapkan di level ini (konsisten dengan project sebelumnya), dan bisa dijadikan materi lanjutan berikutnya.

---

## 7. Timeline Pengerjaan (Saran)

| Pertemuan | Kegiatan                                                         |
|-----------|--------------------------------------------------------------------|
| 1         | Buat database (`users`, `diary`), buat `config.php`, tes koneksi   |
| 2         | Buat `register.php` (dengan `password_hash`) & `login.php` + session |
| 3         | Buat `index.php` (dashboard) + `cek_login.php`                      |
| 4         | Buat `tambah.php` dan `edit.php`                                    |
| 5         | Buat `hapus.php`, `logout.php`, finishing tampilan Bootstrap        |
| 6         | Presentasi/demo project                                            |

---

## 8. Kriteria Penilaian (Opsional)

| Kriteria                                  | Bobot |
|--------------------------------------------|-------|
| Register & login berfungsi (dengan hash)   | 20%   |
| Session & proteksi halaman berjalan        | 15%   |
| Fitur Create diary berjalan                | 15%   |
| Fitur Read (dashboard) berjalan             | 15%   |
| Fitur Update & Delete berjalan              | 15%   |
| Data antar user terpisah dengan benar       | 10%   |
| Tampilan rapi dengan Bootstrap              | 10%   |
