<?php

include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

$id = $_GET['id'];
$d = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM diary WHERE id_diary = '$id' AND user_id = '$user_id'"));

if (isset($_POST['update'])) {
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $tanggal = $_POST['tanggal'];
    mysqli_query($koneksi, "UPDATE diary SET judul = '$judul', isi = '$isi', tanggal = '$tanggal' WHERE id_diary = '$id' AND user_id = '$user_id'");
    header('Location: index.php');
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Diary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5" style="max-width:700px">
    <h2 class="mb-4">Edit Diary</h2>
    <form method="post" class="card card-body shadow-sm">
        <label>Judul</label>
        <input type="text" name="judul" class="form-control mb-3" value="<?= $d['judul'] ?>" required>
        <label>Tanggal</label>
        <input type="date" name="tanggal" class="form-control mb-3" value="<?= $d['tanggal'] ?>" required>
        <label>Isi Diary</label>
        <textarea name="isi" class="form-control mb-3" rows="7" required><?= $d['isi'] ?></textarea>
        <div><button name="update" class="btn btn-warning">Update</button> <a href="index.php" class="btn btn-secondary">Batal</a></div>
    </form>
</main>
</body>
</html>
