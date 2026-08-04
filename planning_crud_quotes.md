# 📋 Planning Backend — CRUD Quotes Sederhana

> **Tech Stack:** PHP (Native) + MySQL + XAMPP  
> **Frontend:** Sudah tersedia (Bootstrap 5)  
> **Tugas Siswa:** Membuat semua file backend PHP

---

## 🗂️ Struktur File yang Harus Dibuat

```
crud3/
├── index.php        ✅ (sudah ada — halaman Login)
├── regis.php        ✅ (sudah ada — halaman Registrasi)
├── dashboard.php    ✅ (sudah ada — halaman List Quotes)
├── tambah.php       ✅ (sudah ada — halaman Form Tambah Quote)
├── profil.php       ✅ (sudah ada — halaman Edit Profil)
├── view.php         ✅ (sudah ada — halaman Detail/List Quotes)
│
├── koneksi.php      ❌ BUAT BARU — file koneksi database
├── proses_login.php ❌ BUAT BARU — proses login
├── proses_regis.php ❌ BUAT BARU — proses registrasi
├── proses_tambah.php❌ BUAT BARU — proses tambah quote
├── proses_hapus.php ❌ BUAT BARU — proses hapus quote
├── proses_edit.php  ❌ BUAT BARU — proses edit quote
├── proses_profil.php❌ BUAT BARU — proses update profil user
└── logout.php       ❌ BUAT BARU — proses logout
```

---

## 🗄️ Step 1 — Buat Database & Tabel

Buka **phpMyAdmin** → buat database baru → jalankan SQL ini:

```sql
-- Buat database
CREATE DATABASE db_quotes;

-- Pilih database
USE db_quotes;

-- Tabel users (untuk login, registrasi, profil)
CREATE TABLE users (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    nama     VARCHAR(100) NOT NULL,
    username VARCHAR(50)  NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Tabel quotes (untuk CRUD quotes)
CREATE TABLE quotes (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    isi_quotes TEXT         NOT NULL,
    id_user    INT          NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);
```

> **Penjelasan kolom:**
> - `users.id` → nomor urut otomatis (primary key)
> - `users.nama` → nama lengkap pengguna
> - `users.username` → nama pengguna untuk login (harus unik)
> - `users.password` → kata sandi (sebaiknya di-hash dengan `password_hash()`)
> - `quotes.id` → nomor urut quote
> - `quotes.isi_quotes` → isi teks quote
> - `quotes.id_user` → menghubungkan quote ke pemiliknya (foreign key)

---

## 🔌 Step 2 — `koneksi.php` (File Koneksi Database)

**Fungsi:** Menghubungkan PHP ke MySQL. File ini di-`include` di semua file backend.

```php
<?php
$host     = "localhost";
$user     = "root";       // sesuaikan dengan username MySQL kamu
$password = "";           // sesuaikan dengan password MySQL kamu
$db       = "db_quotes";

$koneksi = mysqli_connect($host, $user, $password, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
```

---

## 🔐 Step 3 — `proses_regis.php` (Registrasi)

**Dipanggil dari:** `regis.php` (form dengan `name="nama"`, `name="username"`, `name="password"`)

**Alur logika:**
1. Tangkap data dari form (`$_POST`)
2. Cek apakah username sudah ada di database
3. Jika belum ada → simpan data ke tabel `users`
4. Redirect ke `index.php` (halaman login)

**Query SQL yang dibutuhkan:**

```sql
-- Cek apakah username sudah terdaftar
SELECT * FROM users WHERE username = '$username';

-- Simpan user baru
INSERT INTO users (nama, username, password) VALUES ('$nama', '$username', '$password');
```

---

## 🔑 Step 4 — `proses_login.php` (Login)

**Dipanggil dari:** `index.php` (form dengan `name="username"`, `name="password"`)

**Alur logika:**
1. Tangkap `username` dan `password` dari form (`$_POST`)
2. Cari user di database berdasarkan username
3. Jika ditemukan → cocokkan password
4. Jika cocok → simpan data user ke **SESSION** → redirect ke `dashboard.php`
5. Jika tidak cocok → redirect kembali ke `index.php` dengan pesan error

**Query SQL yang dibutuhkan:**

```sql
-- Cari user berdasarkan username
SELECT * FROM users WHERE username = '$username';
```

> **Tips:** Gunakan `session_start()` di bagian atas file, lalu simpan `$_SESSION['id_user']` dan `$_SESSION['nama']`.

---

## 📋 Step 5 — `dashboard.php` (Tampilkan List Quotes) — Modifikasi Frontend

**Fungsi:** Menampilkan semua quotes dari database di dalam tabel.

**Yang perlu ditambahkan di atas `dashboard.php`:**
1. `session_start()` — untuk cek apakah user sudah login
2. Cek SESSION, jika belum login → redirect ke `index.php`
3. Include `koneksi.php`
4. Jalankan query untuk ambil semua quotes

**Query SQL yang dibutuhkan:**

```sql
-- Ambil semua quotes beserta nama pemiliknya
SELECT quotes.id, quotes.isi_quotes, users.nama 
FROM quotes 
JOIN users ON quotes.id_user = users.id;
```

**Yang perlu diubah di tabel HTML `dashboard.php`:**

Ganti header tabel dari:
```html
<th>First</th> <th>Last</th> <th>Handle</th>
```
Menjadi:
```html
<th>No</th> <th>Isi Quotes</th> <th>Penulis</th> <th>Aksi</th>
```

Dan isi tabel menggunakan PHP loop:
```php
<?php
$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
        <td>{$no}</td>
        <td>{$row['isi_quotes']}</td>
        <td>{$row['nama']}</td>
        <td>
            <a href='proses_hapus.php?id={$row['id']}' class='btn btn-danger btn-sm'>Hapus</a>
            <a href='view.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
        </td>
    </tr>";
    $no++;
}
?>
```

---

## ➕ Step 6 — `proses_tambah.php` (Tambah Quote)

**Dipanggil dari:** `tambah.php` (form dengan `name="quotes"`)

**Alur logika:**
1. Cek SESSION → pastikan user sudah login
2. Tangkap `isi_quotes` dari `$_POST['quotes']`
3. Ambil `id_user` dari `$_SESSION['id_user']`
4. Simpan ke database
5. Redirect ke `dashboard.php`

**Query SQL yang dibutuhkan:**

```sql
-- Simpan quote baru
INSERT INTO quotes (isi_quotes, id_user) VALUES ('$isi_quotes', $id_user);
```

---

## 👁️ Step 7 — `view.php` (Halaman Edit Quote)

**Fungsi:** Menampilkan form edit berisi data quote yang dipilih.

**Cara kerjanya:**
1. Terima `id` dari URL (`$_GET['id']`)
2. Ambil data quote dari database berdasarkan `id`
3. Tampilkan di form (nilai default dari database)
4. Saat form di-submit → kirim ke `proses_edit.php`

**Query SQL yang dibutuhkan:**

```sql
-- Ambil 1 quote berdasarkan id
SELECT * FROM quotes WHERE id = '$id';
```

**Contoh form di `view.php`:**
```html
<form method="post" action="proses_edit.php">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <textarea name="quotes"><?= $row['isi_quotes'] ?></textarea>
    <button type="submit">Update</button>
</form>
```

---

## ✏️ Step 8 — `proses_edit.php` (Proses Update Quote)

**Dipanggil dari:** form di `view.php`

**Alur logika:**
1. Tangkap `id` dan `isi_quotes` dari `$_POST`
2. Jalankan query UPDATE
3. Redirect ke `dashboard.php`

**Query SQL yang dibutuhkan:**

```sql
-- Update isi quote berdasarkan id
UPDATE quotes SET isi_quotes = '$isi_quotes' WHERE id = $id;
```

---

## 🗑️ Step 9 — `proses_hapus.php` (Hapus Quote)

**Dipanggil dari:** link di tabel `dashboard.php` → `proses_hapus.php?id=X`

**Alur logika:**
1. Tangkap `id` dari URL (`$_GET['id']`)
2. Jalankan query DELETE
3. Redirect ke `dashboard.php`

**Query SQL yang dibutuhkan:**

```sql
-- Hapus quote berdasarkan id
DELETE FROM quotes WHERE id = $id;
```

---

## 👤 Step 10 — `proses_profil.php` (Update Profil)

**Dipanggil dari:** `profil.php` (form dengan `name="nama"`, `name="username"`, `name="password"`)

**Alur logika:**
1. Cek SESSION → pastikan user sudah login
2. Tangkap data dari `$_POST`
3. Ambil `id_user` dari `$_SESSION['id_user']`
4. Jalankan query UPDATE pada tabel `users`
5. Update juga data di SESSION
6. Redirect ke `profil.php`

**Query SQL yang dibutuhkan:**

```sql
-- Update data profil user
UPDATE users SET nama = '$nama', username = '$username', password = '$password' 
WHERE id = $id_user;
```

---

## 🚪 Step 11 — `logout.php` (Logout)

**Dipanggil dari:** navbar link `logout.php`

**Alur logika:**
1. Mulai session
2. Hapus semua data session
3. Redirect ke `index.php`

```php
<?php
session_start();
session_destroy();
header("Location: index.php");
exit;
?>
```

---

## 🔒 Step 12 — Proteksi Halaman (Session Guard)

Tambahkan kode ini di **bagian paling atas** setiap halaman yang membutuhkan login (`dashboard.php`, `tambah.php`, `profil.php`, `view.php`):

```php
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit;
}
include 'koneksi.php';
?>
```

---

## 📊 Diagram Alur Aplikasi

```
[regis.php] → [proses_regis.php] → [index.php]
                                         ↓
[index.php] → [proses_login.php] → [dashboard.php]
                                         ↓
                        ┌────────────────┼────────────────┐
                        ↓                ↓                ↓
                  [tambah.php]      [view.php]       [proses_hapus.php]
                        ↓                ↓
               [proses_tambah.php] [proses_edit.php]
                        ↓                ↓
                   [dashboard.php] ←─────┘

[profil.php] → [proses_profil.php] → [profil.php]
[logout.php] → [index.php]
```

---

## ✅ Checklist Tugas Siswa

- [ ] Buat database `db_quotes` dan 2 tabel (`users`, `quotes`) di phpMyAdmin
- [ ] Buat file `koneksi.php`
- [ ] Buat file `proses_regis.php` + pasang `action` di `regis.php`
- [ ] Buat file `proses_login.php` + pasang `action` di `index.php`
- [ ] Modifikasi `dashboard.php` → tambah session guard + tampilkan data dari DB
- [ ] Buat file `proses_tambah.php` + pasang `action` di `tambah.php`
- [ ] Modifikasi `view.php` → tampilkan form edit dengan data dari DB
- [ ] Buat file `proses_edit.php`
- [ ] Buat file `proses_hapus.php`
- [ ] Modifikasi `profil.php` → tampilkan data user dari SESSION + buat `proses_profil.php`
- [ ] Buat file `logout.php`
- [ ] Test semua fitur dari awal (registrasi → login → CRUD → logout)
