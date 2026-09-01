# Planning Project: Aplikasi Inventaris Barang Toko (CRUD PHP Native + Bootstrap)

> Project pembelajaran untuk kelas 11 RPL — mata pelajaran Pemrograman Web
> Materi: CRUD (Create, Read, Update, Delete) menggunakan **PHP Native** + **Bootstrap**
> Catatan: Project ini **belum** menerapkan filter keamanan (XSS/SQL Injection) karena materi tersebut belum diajarkan. Fokus project ini murni pada logika dasar CRUD.

---

## 1. Deskripsi Project

Membuat aplikasi sederhana untuk mengelola data **Inventaris Barang Toko** dengan field:

| Field      | Tipe Data     | Keterangan                  |
|------------|---------------|------------------------------|
| id_barang  | INT (PK, AI)  | Primary key, auto increment |
| nama_barang| VARCHAR(100)  | Nama barang                 |
| harga      | INT / DECIMAL | Harga barang                |
| stok       | INT           | Jumlah stok barang           |

Fitur yang harus ada:
- Tambah data barang
- Tampil semua data barang
- Edit data barang
- Hapus data barang

---

## 2. Struktur Folder Project

```
inventaris-toko/
│
├── config.php        # Koneksi ke database
├── index.php         # Halaman utama, menampilkan semua data
├── tambah.php         # Form + proses tambah data
├── edit.php           # Form + proses edit data
├── hapus.php          # Proses hapus data
└── assets/
    └── (opsional, jika mau taruh CSS/JS tambahan)
```

Untuk Bootstrap, cukup gunakan CDN (tidak perlu download file), supaya lebih ringan untuk siswa.

---

## 3. Struktur Database (Perintah SQL Lengkap)

Buat database dan tabel lewat phpMyAdmin (tab SQL) atau terminal MySQL:

```sql
-- 1. Buat database
CREATE DATABASE db_inventaris;

-- 2. Gunakan database
USE db_inventaris;

-- 3. Buat tabel barang
CREATE TABLE barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    harga INT NOT NULL,
    stok INT NOT NULL
);

-- 4. (Opsional) Contoh data awal
INSERT INTO barang (nama_barang, harga, stok) VALUES
('Buku Tulis', 5000, 100),
('Pulpen', 2000, 150),
('Penggaris', 3000, 80);
```

---

## 4. Step-by-Step Pembuatan

### Step 1 — Koneksi Database (`config.php`)

```php
<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "db_inventaris";

$koneksi = mysqli_connect($host, $user, $pass, $dbname);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
```

**Penjelasan kode:**
- `mysqli_connect()` adalah fungsi PHP native untuk menghubungkan PHP ke MySQL, dengan 4 parameter: host, username, password, nama database.
- `die()` menghentikan eksekusi script dan menampilkan pesan jika koneksi gagal — ini berguna untuk debugging tahap awal.
- File ini akan dipanggil (`include`/`require`) di semua file lain yang butuh akses database.

Referensi: [PHP MySQL Connect – W3Schools](https://www.w3schools.com/php/php_mysql_connect.asp)

---

### Step 2 — Menampilkan Data (`index.php`)

```php
<?php include "config.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Inventaris Toko</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2>Data Inventaris Barang</h2>
    <a href="tambah.php" class="btn btn-primary mb-3">+ Tambah Barang</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $query = mysqli_query($koneksi, "SELECT * FROM barang");
            while ($data = mysqli_fetch_assoc($query)) {
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $data['nama_barang'] ?></td>
                <td><?= number_format($data['harga']) ?></td>
                <td><?= $data['stok'] ?></td>
                <td>
                    <a href="edit.php?id=<?= $data['id_barang'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="hapus.php?id=<?= $data['id_barang'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
```

**Penjelasan kode:**
- `include "config.php"` memuat koneksi database di awal file.
- `mysqli_query()` menjalankan perintah SQL `SELECT * FROM barang` untuk mengambil semua data.
- `mysqli_fetch_assoc()` mengubah 1 baris hasil query menjadi array asosiatif (`$data['nama_barang']`, dst), dipanggil di dalam `while` supaya looping semua baris data.
- `<?= ... ?>` adalah shorthand dari `<?php echo ... ?>`.
- `number_format()` merapikan format angka harga (misal `15000` → `15,000`).
- Link **Hapus** memakai `onclick="return confirm(...)"` — konfirmasi sederhana pakai JavaScript bawaan browser, sebelum benar-benar redirect ke `hapus.php`.

Referensi: [PHP MySQL Select – W3Schools](https://www.w3schools.com/php/php_mysql_select.asp)

---

### Step 3 — Tambah Data (`tambah.php`)

```php
<?php
include "config.php";

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $query = "INSERT INTO barang (nama_barang, harga, stok) VALUES ('$nama', '$harga', '$stok')";
    mysqli_query($koneksi, $query);

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2>Tambah Barang</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Harga</label>
            <input type="number" name="harga" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" required>
        </div>
        <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
```

**Penjelasan kode:**
- `isset($_POST['simpan'])` mengecek apakah form sudah disubmit (tombol `name="simpan"` ditekan).
- Data dari form (`$_POST['nama_barang']`, dst) langsung dimasukkan ke query `INSERT INTO` — di tahap ini **belum** ada `mysqli_real_escape_string()` atau prepared statement, karena materi keamanan belum diajarkan.
- `header("Location: index.php")` mengarahkan kembali ke halaman utama setelah data tersimpan, lalu `exit` menghentikan eksekusi script setelah redirect.
- Atribut `required` di HTML hanya validasi sisi browser (belum validasi sisi server).

Referensi: [PHP MySQL Insert – W3Schools](https://www.w3schools.com/php/php_mysql_insert.asp)

---

### Step 4 — Edit Data (`edit.php`)

```php
<?php
include "config.php";

$id = $_GET['id'];

if (isset($_POST['update'])) {
    $nama = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $query = "UPDATE barang SET nama_barang='$nama', harga='$harga', stok='$stok' WHERE id_barang='$id'";
    mysqli_query($koneksi, $query);

    header("Location: index.php");
    exit;
}

$dataBarang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang='$id'"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2>Edit Barang</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" value="<?= $dataBarang['nama_barang'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Harga</label>
            <input type="number" name="harga" class="form-control" value="<?= $dataBarang['harga'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" value="<?= $dataBarang['stok'] ?>" required>
        </div>
        <button type="submit" name="update" class="btn btn-warning">Update</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
```

**Penjelasan kode:**
- `$_GET['id']` mengambil id barang dari URL (contoh: `edit.php?id=3`), dikirim dari link Edit di `index.php`.
- Query `SELECT ... WHERE id_barang='$id'` mengambil 1 data spesifik untuk ditampilkan di form (pre-filled), memakai atribut `value="..."` di setiap input.
- Saat form disubmit (`isset($_POST['update'])`), data baru menggantikan data lama lewat `UPDATE ... SET ... WHERE id_barang='$id'`.
- Struktur logikanya mirip `tambah.php`, bedanya di sini ada proses ambil data lama dulu + query yang dipakai adalah `UPDATE`, bukan `INSERT`.

Referensi: [PHP MySQL Update – W3Schools](https://www.w3schools.com/php/php_mysql_update.asp)

---

### Step 5 — Hapus Data (`hapus.php`)

```php
<?php
include "config.php";

$id = $_GET['id'];

$query = "DELETE FROM barang WHERE id_barang='$id'";
mysqli_query($koneksi, $query);

header("Location: index.php");
exit;
?>
```

**Penjelasan kode:**
- File ini tidak punya tampilan HTML — murni proses backend.
- Mengambil `id` dari URL, lalu langsung menjalankan query `DELETE FROM barang WHERE id_barang='$id'`.
- Setelah proses selesai, langsung redirect (`header("Location: ...")`) kembali ke `index.php`.
- Konfirmasi hapus sudah ditangani sebelumnya di `index.php` lewat `confirm()` JavaScript, jadi file ini fokus hanya di query hapus.

Referensi: [PHP MySQL Delete – W3Schools](https://www.w3schools.com/php/php_mysql_delete.asp)

---

## 5. Catatan Keamanan (Penting untuk Siswa)

Project ini **sengaja dibuat sederhana** dan native, sesuai tahap belajar siswa saat ini:

- ❌ Belum ada `mysqli_real_escape_string()` / prepared statement → rentan **SQL Injection**
- ❌ Belum ada `htmlspecialchars()` saat menampilkan data → rentan **XSS**
- ❌ Belum ada validasi input di sisi server (baru validasi HTML `required`)

> Ini boleh dijelaskan ke siswa sebagai **catatan pengembangan lanjutan**, supaya mereka sadar bahwa versi ini adalah versi belajar dasar, dan akan disempurnakan di materi keamanan web berikutnya (prepared statement, sanitasi input, dsb).

---

## 6. Referensi Belajar (W3Schools PHP MySQL)

- [PHP MySQL Connect](https://www.w3schools.com/php/php_mysql_connect.asp)
- [PHP MySQL Create Table](https://www.w3schools.com/php/php_mysql_create_table.asp)
- [PHP MySQL Insert Data](https://www.w3schools.com/php/php_mysql_insert.asp)
- [PHP MySQL Select Data](https://www.w3schools.com/php/php_mysql_select.asp)
- [PHP MySQL Update Data](https://www.w3schools.com/php/php_mysql_update.asp)
- [PHP MySQL Delete Data](https://www.w3schools.com/php/php_mysql_delete.asp)

---

## 7. Timeline Pengerjaan (Saran)

| Pertemuan | Kegiatan                                         |
|-----------|---------------------------------------------------|
| 1         | Buat database & tabel, buat `config.php`, tes koneksi |
| 2         | Buat `index.php` (tampil data) + styling Bootstrap dasar |
| 3         | Buat `tambah.php` (Create) dan uji coba input data |
| 4         | Buat `edit.php` dan `hapus.php` (Update & Delete)  |
| 5         | Finishing tampilan + presentasi/demo project        |

---

## 8. Kriteria Penilaian (Opsional)

| Kriteria                          | Bobot |
|-----------------------------------|-------|
| Struktur database sesuai          | 10%   |
| Fitur Create berjalan             | 20%   |
| Fitur Read (tampil data) berjalan | 20%   |
| Fitur Update berjalan             | 20%   |
| Fitur Delete berjalan             | 15%   |
| Tampilan rapi dengan Bootstrap    | 10%   |
| Kerapian kode & penamaan variabel | 5%    |
