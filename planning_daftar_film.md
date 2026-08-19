# 🎬 Planning Project: CRUD Daftar Film
**Teknologi:** PHP Native + Bootstrap 5  
**Tanggal Mulai:** 2026-08-19  
**Level:** Pemula (Belajar CRUD Pertama)

---

## 📌 GAMBARAN UMUM PROJECT

Project ini adalah aplikasi web sederhana untuk mengelola **daftar film** yang dilengkapi dengan sistem autentikasi (Registrasi & Login). Setiap user yang sudah login dapat menambah, melihat, mengedit, dan menghapus data film miliknya sendiri.

---

## 🗂️ STRUKTUR FOLDER PROJECT

```
daftar_film/
│
├── config/
│   └── db.php                  ← Koneksi database
│
├── auth/
│   ├── register.php            ← Halaman registrasi
│   ├── register_proses.php     ← Proses simpan data register
│   ├── login.php               ← Halaman login
│   ├── login_proses.php        ← Proses cek login
│   └── logout.php              ← Proses logout
│
├── film/
│   ├── index.php               ← Daftar semua film (READ)
│   ├── tambah.php              ← Form tambah film (CREATE - form)
│   ├── tambah_proses.php       ← Proses simpan film baru (CREATE - proses)
│   ├── edit.php                ← Form edit film (UPDATE - form)
│   ├── edit_proses.php         ← Proses update film (UPDATE - proses)
│   └── hapus.php               ← Proses hapus film (DELETE)
│
└── index.php                   ← Redirect ke login jika belum login
```

---

## 🗄️ TAHAP 1 — DATABASE & TABEL

### Langkah 1.1 — Buat Database
Buka **phpMyAdmin** → klik "New" → buat database:

```sql
CREATE DATABASE daftar_film;
USE daftar_film;
```

---

### Langkah 1.2 — Buat Tabel `users`
Tabel untuk menyimpan data akun pengguna.

```sql
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

| Kolom       | Tipe         | Keterangan                       |
|-------------|--------------|----------------------------------|
| id          | INT          | Primary key, auto increment      |
| nama        | VARCHAR(100) | Nama lengkap user                |
| email       | VARCHAR(150) | Email unik untuk login           |
| password    | VARCHAR(255) | Password yang sudah di-hash      |
| created_at  | TIMESTAMP    | Waktu registrasi otomatis        |

---

### Langkah 1.3 — Buat Tabel `film`
Tabel untuk menyimpan data film.

```sql
CREATE TABLE film (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT           NOT NULL,
    judul       VARCHAR(200)  NOT NULL,
    genre       VARCHAR(100)  NOT NULL,
    tahun       YEAR          NOT NULL,
    sutradara   VARCHAR(150)  NOT NULL,
    sinopsis    TEXT,
    rating      DECIMAL(3,1)  DEFAULT 0.0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

| Kolom      | Tipe         | Keterangan                            |
|------------|--------------|---------------------------------------|
| id         | INT          | Primary key, auto increment           |
| user_id    | INT          | FK ke tabel users (pemilik film)      |
| judul      | VARCHAR(200) | Judul film                            |
| genre      | VARCHAR(100) | Genre film (Action, Drama, dll)       |
| tahun      | YEAR         | Tahun rilis film                      |
| sutradara  | VARCHAR(150) | Nama sutradara                        |
| sinopsis   | TEXT         | Ringkasan cerita film                 |
| rating     | DECIMAL(3,1) | Rating film (0.0 - 10.0)             |
| created_at | TIMESTAMP    | Waktu data dibuat                     |

---

## ⚙️ TAHAP 2 — KONFIGURASI KONEKSI DATABASE

### File: `config/db.php`

```php
<?php
$host     = "localhost";
$user     = "root";       // sesuaikan dengan username MySQL kamu
$password = "";           // sesuaikan dengan password MySQL kamu
$database = "daftar_film";

// Buat koneksi
$conn = mysqli_connect($host, $user, $password, $database);

// Cek apakah koneksi berhasil
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
```

> 💡 **Catatan:** File ini akan di-`include` di setiap halaman yang butuh akses database.

---

## 🔐 TAHAP 3 — FITUR REGISTRASI

### Langkah 3.1 — Form Registrasi
**File: `auth/register.php`**

```php
<?php
// Jika sudah login, langsung ke halaman film
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../film/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi - Daftar Film</title>
    <link rel="stylesheet" 
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>🎬 Daftar Akun Baru</h4>
                </div>
                <div class="card-body">

                    <!-- Tampilkan pesan error jika ada -->
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_GET['error']) ?>
                        </div>
                    <?php endif; ?>

                    <form action="register_proses.php" method="POST">
                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Daftar</button>
                    </form>

                    <hr>
                    <p class="text-center">Sudah punya akun? 
                        <a href="login.php">Login di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
```

---

### Langkah 3.2 — Proses Simpan Registrasi
**File: `auth/register_proses.php`**

```php
<?php
session_start();
require_once "../config/db.php";

// Ambil data dari form
$nama     = trim($_POST['nama']);
$email    = trim($_POST['email']);
$password = $_POST['password'];

// --- VALIDASI ---

// 1. Cek apakah email sudah terdaftar
$cek = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
if (mysqli_num_rows($cek) > 0) {
    header("Location: register.php?error=Email sudah terdaftar!");
    exit();
}

// 2. Enkripsi password sebelum disimpan (WAJIB! Jangan simpan plain text)
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// --- SIMPAN KE DATABASE ---
$query = "INSERT INTO users (nama, email, password) 
          VALUES ('$nama', '$email', '$password_hash')";

if (mysqli_query($conn, $query)) {
    // Registrasi berhasil → redirect ke login
    header("Location: login.php?success=Registrasi berhasil! Silakan login.");
    exit();
} else {
    header("Location: register.php?error=Registrasi gagal, coba lagi.");
    exit();
}
?>
```

> 🔑 **Penting:** `password_hash()` mengubah password menjadi kode acak yang aman. Ini WAJIB dilakukan — jangan pernah simpan password asli di database!

---

## 🔑 TAHAP 4 — FITUR LOGIN

### Langkah 4.1 — Form Login
**File: `auth/login.php`**

```php
<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../film/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Daftar Film</title>
    <link rel="stylesheet" 
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-dark text-white text-center">
                    <h4>🎬 Login Daftar Film</h4>
                </div>
                <div class="card-body">

                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($_GET['success']) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_GET['error']) ?>
                        </div>
                    <?php endif; ?>

                    <form action="login_proses.php" method="POST">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Login</button>
                    </form>

                    <hr>
                    <p class="text-center">Belum punya akun? 
                        <a href="register.php">Daftar di sini</a>
                    p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
```

---

### Langkah 4.2 — Proses Login
**File: `auth/login_proses.php`**

```php
<?php
session_start();
require_once "../config/db.php";

$email    = trim($_POST['email']);
$password = $_POST['password'];

// Cari user berdasarkan email
$query  = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);

    // Verifikasi password dengan password_verify()
    // (kebalikan dari password_hash() saat register)
    if (password_verify($password, $user['password'])) {

        // Login BERHASIL → simpan data user ke SESSION
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['email']   = $user['email'];

        // Arahkan ke halaman daftar film
        header("Location: ../film/index.php");
        exit();

    } else {
        // Password salah
        header("Location: login.php?error=Password salah!");
        exit();
    }
} else {
    // Email tidak ditemukan
    header("Location: login.php?error=Email tidak terdaftar!");
    exit();
}
?>
```

---

### Langkah 4.3 — Logout
**File: `auth/logout.php`**

```php
<?php
session_start();
session_destroy();  // Hapus semua data session
header("Location: ../auth/login.php");
exit();
?>
```

---

## 🛡️ TAHAP 5 — PROTEKSI HALAMAN (WAJIB!)

Setiap halaman yang hanya boleh diakses setelah login **HARUS** memiliki kode proteksi ini di baris **paling atas**:

```php
<?php
session_start();
// Jika belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>
```

> ⚠️ **Jika tidak ada proteksi ini**, siapa pun bisa langsung akses halaman film tanpa login!

---

## 🎥 TAHAP 6 — CRUD FILM

### Langkah 6.1 — READ: Tampilkan Daftar Film
**File: `film/index.php`**

```php
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once "../config/db.php";

// Ambil semua film MILIK USER yang sedang login
$user_id = $_SESSION['user_id'];
$query   = "SELECT * FROM film WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result  = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Film Saya</title>
    <link rel="stylesheet" 
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand">🎬 Daftar Film</a>
        <div>
            <span class="text-white me-3">Halo, <?= $_SESSION['nama'] ?>!</span>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<!-- KONTEN -->
<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h3>🎞️ Film Saya</h3>
        <a href="tambah.php" class="btn btn-primary">+ Tambah Film</a>
    </div>

    <!-- Pesan sukses setelah aksi -->
    <?php if (isset($_GET['pesan'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['pesan']) ?></div>
    <?php endif; ?>

    <!-- TABEL DAFTAR FILM -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Genre</th>
                <th>Tahun</th>
                <th>Sutradara</th>
                <th>Rating</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($film = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($film['judul']) ?></td>
                <td><?= htmlspecialchars($film['genre']) ?></td>
                <td><?= $film['tahun'] ?></td>
                <td><?= htmlspecialchars($film['sutradara']) ?></td>
                <td>⭐ <?= $film['rating'] ?></td>
                <td>
                    <a href="edit.php?id=<?= $film['id'] ?>" 
                       class="btn btn-warning btn-sm">Edit</a>
                    <a href="hapus.php?id=<?= $film['id'] ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Yakin hapus film ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>

            <?php if (mysqli_num_rows($result) == 0): ?>
            <tr>
                <td colspan="7" class="text-center text-muted">
                    Belum ada film. Tambahkan film pertamamu!
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
```

---

### Langkah 6.2 — CREATE: Form Tambah Film
**File: `film/tambah.php`**

```php
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Film</title>
    <link rel="stylesheet" 
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3>Tambah Film Baru</h3>
    <hr>
    <form action="tambah_proses.php" method="POST">
        <div class="mb-3">
            <label>Judul Film</label>
            <input type="text" name="judul" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Genre</label>
            <select name="genre" class="form-select" required>
                <option value="">-- Pilih Genre --</option>
                <option value="Action">Action</option>
                <option value="Drama">Drama</option>
                <option value="Comedy">Comedy</option>
                <option value="Horror">Horror</option>
                <option value="Romance">Romance</option>
                <option value="Sci-Fi">Sci-Fi</option>
                <option value="Thriller">Thriller</option>
                <option value="Animation">Animation</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Tahun Rilis</label>
            <input type="number" name="tahun" class="form-control" 
                   min="1900" max="2030" required>
        </div>
        <div class="mb-3">
            <label>Sutradara</label>
            <input type="text" name="sutradara" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Sinopsis</label>
            <textarea name="sinopsis" class="form-control" rows="4"></textarea>
        </div>
        <div class="mb-3">
            <label>Rating (0 - 10)</label>
            <input type="number" name="rating" class="form-control" 
                   min="0" max="10" step="0.1">
        </div>
        <a href="index.php" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Film</button>
    </form>
</div>
</body>
</html>
```

---

### Langkah 6.3 — CREATE: Proses Simpan Film
**File: `film/tambah_proses.php`**

```php
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once "../config/db.php";

// Ambil data dari form
$user_id   = $_SESSION['user_id'];
$judul     = trim($_POST['judul']);
$genre     = trim($_POST['genre']);
$tahun     = $_POST['tahun'];
$sutradara = trim($_POST['sutradara']);
$sinopsis  = trim($_POST['sinopsis']);
$rating    = $_POST['rating'];

// Query INSERT untuk menyimpan film baru
$query = "INSERT INTO film (user_id, judul, genre, tahun, sutradara, sinopsis, rating)
          VALUES ('$user_id', '$judul', '$genre', '$tahun', '$sutradara', '$sinopsis', '$rating')";

if (mysqli_query($conn, $query)) {
    header("Location: index.php?pesan=Film berhasil ditambahkan!");
} else {
    header("Location: tambah.php?error=Gagal menambah film.");
}
exit();
?>
```

---

### Langkah 6.4 — UPDATE: Form Edit Film
**File: `film/edit.php`**

```php
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once "../config/db.php";

// Ambil ID film dari URL (?id=...)
$id      = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Ambil data film yang akan diedit
// Pastikan film ini MILIK user yang sedang login (WHERE user_id = ...)
$query  = "SELECT * FROM film WHERE id = '$id' AND user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$film   = mysqli_fetch_assoc($result);

// Jika film tidak ditemukan (atau bukan miliknya), redirect
if (!$film) {
    header("Location: index.php?pesan=Film tidak ditemukan!");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Film</title>
    <link rel="stylesheet" 
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3>Edit Film</h3>
    <hr>
    <form action="edit_proses.php" method="POST">
        <!-- ID film dikirim sebagai hidden field -->
        <input type="hidden" name="id" value="<?= $film['id'] ?>">

        <div class="mb-3">
            <label>Judul Film</label>
            <!-- value diisi dengan data lama dari database -->
            <input type="text" name="judul" class="form-control" 
                   value="<?= htmlspecialchars($film['judul']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Genre</label>
            <select name="genre" class="form-select">
                <?php
                $genres = ['Action','Drama','Comedy','Horror','Romance','Sci-Fi','Thriller','Animation'];
                foreach ($genres as $g) {
                    // Tandai option yang sesuai dengan data lama
                    $selected = ($film['genre'] == $g) ? 'selected' : '';
                    echo "<option value='$g' $selected>$g</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Tahun Rilis</label>
            <input type="number" name="tahun" class="form-control" 
                   value="<?= $film['tahun'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Sutradara</label>
            <input type="text" name="sutradara" class="form-control" 
                   value="<?= htmlspecialchars($film['sutradara']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Sinopsis</label>
            <textarea name="sinopsis" class="form-control" 
                      rows="4"><?= htmlspecialchars($film['sinopsis']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Rating</label>
            <input type="number" name="rating" class="form-control" 
                   min="0" max="10" step="0.1" value="<?= $film['rating'] ?>">
        </div>
        <a href="index.php" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-warning">Update Film</button>
    </form>
</div>
</body>
</html>
```

---

### Langkah 6.5 — UPDATE: Proses Simpan Edit
**File: `film/edit_proses.php`**

```php
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once "../config/db.php";

// Ambil data dari form
$id        = $_POST['id'];
$user_id   = $_SESSION['user_id'];
$judul     = trim($_POST['judul']);
$genre     = trim($_POST['genre']);
$tahun     = $_POST['tahun'];
$sutradara = trim($_POST['sutradara']);
$sinopsis  = trim($_POST['sinopsis']);
$rating    = $_POST['rating'];

// Query UPDATE
// Pastikan hanya update film MILIK user yang login (AND user_id = ...)
$query = "UPDATE film 
          SET judul     = '$judul',
              genre     = '$genre',
              tahun     = '$tahun',
              sutradara = '$sutradara',
              sinopsis  = '$sinopsis',
              rating    = '$rating'
          WHERE id = '$id' AND user_id = '$user_id'";

if (mysqli_query($conn, $query)) {
    header("Location: index.php?pesan=Film berhasil diupdate!");
} else {
    header("Location: edit.php?id=$id&error=Gagal update film.");
}
exit();
?>
```

---

### Langkah 6.6 — DELETE: Hapus Film
**File: `film/hapus.php`**

```php
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once "../config/db.php";

$id      = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Query DELETE
// Pastikan hanya hapus film MILIK user yang login (AND user_id = ...)
$query = "DELETE FROM film WHERE id = '$id' AND user_id = '$user_id'";

if (mysqli_query($conn, $query)) {
    header("Location: index.php?pesan=Film berhasil dihapus!");
} else {
    header("Location: index.php?pesan=Gagal menghapus film.");
}
exit();
?>
```

---

## 🚀 TAHAP 7 — FILE UTAMA (ROOT)

### File: `index.php` (di root folder)

```php
<?php
session_start();
// Jika sudah login ke daftar film, jika belum ke login
if (isset($_SESSION['user_id'])) {
    header("Location: film/index.php");
} else {
    header("Location: auth/login.php");
}
exit();
?>
```

---

## 📋 RINGKASAN QUERY SQL YANG DIGUNAKAN

| Operasi       | Query SQL                                                         |
|---------------|-------------------------------------------------------------------|
| **SELECT**    | `SELECT * FROM film WHERE user_id = '$id'`                        |
| **INSERT**    | `INSERT INTO film (kolom1, kolom2) VALUES (val1, val2)`           |
| **UPDATE**    | `UPDATE film SET kolom1='val' WHERE id='$id' AND user_id='$uid'`  |
| **DELETE**    | `DELETE FROM film WHERE id='$id' AND user_id='$uid'`              |
| **Cek email** | `SELECT id FROM users WHERE email='$email'`                       |

---

## 🔄 ALUR APLIKASI (FLOW)

```
User buka website
        |
        v
    [index.php]
        |
        |-- Sudah login? --> YA --> [film/index.php] --> Tampil daftar film
        |                                  |
        |                       .----------+----------.
        |                    Tambah      Edit       Hapus
        |                   (CREATE)   (UPDATE)   (DELETE)
        |
        |-- Belum login? --> TIDAK --> [auth/login.php]
                                              |
                                       .------+------.
                                     Login        Belum punya akun?
                                       |                  |
                                 [Masuk langsung]   [register.php]
                                                          |
                                                   [Daftar Akun]
                                                          |
                                                   [Kembali Login]
```

---

## CHECKLIST URUTAN PENGERJAAN

```
[ ] Step 1  : Buat database dan tabel (users & film) di phpMyAdmin
[ ] Step 2  : Buat folder struktur project di htdocs/daftar_film/
[ ] Step 3  : Buat file config/db.php (koneksi database)
[ ] Step 4  : Buat auth/register.php (form registrasi)
[ ] Step 5  : Buat auth/register_proses.php (proses simpan register)
[ ] Step 6  : Buat auth/login.php (form login)
[ ] Step 7  : Buat auth/login_proses.php (proses cek login)
[ ] Step 8  : Buat auth/logout.php
[ ] Step 9  : Buat film/index.php (daftar film - READ)
[ ] Step 10 : Buat film/tambah.php + tambah_proses.php (CREATE)
[ ] Step 11 : Buat film/edit.php + edit_proses.php (UPDATE)
[ ] Step 12 : Buat film/hapus.php (DELETE)
[ ] Step 13 : Buat index.php di root folder (redirect otomatis)
[ ] Step 14 : Test semua fitur satu per satu di browser
```

---

## 🧠 KONSEP PENTING YANG DIPELAJARI

| Konsep                 | Penjelasan Singkat                                              |
|------------------------|-----------------------------------------------------------------|
| `session_start()`      | Wajib dipanggil di atas setiap file yang pakai session          |
| `$_SESSION`            | Menyimpan data user yang sedang login                           |
| `$_POST`               | Menerima data dari form (method="POST")                         |
| `$_GET`                | Menerima data dari URL (contoh: ?id=5)                          |
| `password_hash()`      | Mengamankan password sebelum disimpan ke database               |
| `password_verify()`    | Mengecek password saat login (pasangan dari password_hash)      |
| `mysqli_query()`       | Menjalankan query SQL ke database                               |
| `mysqli_fetch_assoc()` | Mengambil satu baris data hasil query menjadi array             |
| `mysqli_num_rows()`    | Menghitung jumlah baris hasil query                             |
| `header("Location:")`  | Redirect / pindah ke halaman lain                               |
| `htmlspecialchars()`   | Mencegah XSS - jaga keamanan output ke HTML                     |
| `require_once`         | Include file lain (db.php) tanpa duplikasi                      |

---

## 🎓 TIPS UNTUK PEMULA

1. **Kerjakan urut dari atas ke bawah** - jangan loncat-loncat
2. **Test setiap step** - setelah buat 1 file, langsung test di browser
3. **Baca pesan error** - PHP selalu kasih tahu dimana salahnya
4. **Pahami query SQL dulu** - coba query di phpMyAdmin sebelum tulis di PHP
5. **Jangan copy-paste buta** - ketik manual agar cepat hafal dan paham
6. **Selalu cek session** - pastikan proteksi halaman ada di setiap file film/

---

*Planning dibuat: 2026-08-19 | Teknologi: PHP Native + Bootstrap 5 | Level: Pemula*
