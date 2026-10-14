<?php
include "cek_login.php";
include "koneksi.php";

$edit_data = null;

// ==== HAPUS ====
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM sampah WHERE id_sampah='$id'");
    header("Location: sampah.php");
    exit;
}

// ==== TAMBAH / UPDATE ====
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis_sampah = mysqli_real_escape_string($koneksi, $_POST['jenis_sampah']);
    $harga        = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok_kg      = mysqli_real_escape_string($koneksi, $_POST['stok_kg']);

    if (!empty($_POST['id_sampah'])) {
        // UPDATE
        $id = mysqli_real_escape_string($koneksi, $_POST['id_sampah']);
        mysqli_query($koneksi, "UPDATE sampah SET jenis_sampah='$jenis_sampah', harga='$harga', stok_kg='$stok_kg' WHERE id_sampah='$id'");
    } else {
        // INSERT
        mysqli_query($koneksi, "INSERT INTO sampah (jenis_sampah, harga, stok_kg) VALUES ('$jenis_sampah','$harga','$stok_kg')");
    }
    header("Location: sampah.php");
    exit;
}

// ==== AMBIL DATA UNTUK EDIT ====
if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $result = mysqli_query($koneksi, "SELECT * FROM sampah WHERE id_sampah='$id'");
    $edit_data = mysqli_fetch_assoc($result);
}

// ==== AMBIL SEMUA DATA ====
$data = mysqli_query($koneksi, "SELECT * FROM sampah ORDER BY id_sampah DESC");
if (!$data) {
    die("Query gagal: " . mysqli_error($koneksi) . " — cek apakah tabel 'sampah' sudah ada di database '$db'.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Menu Sampah</title>
<style>
    body { font-family: Arial, sans-serif; margin: 40px; background: #f4f4f4; }
    h2 { color: #333; }
    .container { display: flex; gap: 30px; }
    form { background: #fff; padding: 20px; border-radius: 6px; width: 300px; height: fit-content; }
    input { width: 100%; padding: 8px; margin-bottom: 12px; box-sizing: border-box; }
    button { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; }
    .btn-simpan { background: #4CAF50; }
    .btn-batal { background: #999; text-decoration: none; padding: 10px 15px; border-radius: 4px; display: inline-block; margin-left: 5px; color: white; }
    table { width: 100%; border-collapse: collapse; background: #fff; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background: #4CAF50; color: white; }
    .btn { padding: 5px 10px; text-decoration: none; border-radius: 4px; color: white; font-size: 13px; }
    .btn-edit { background: #2196F3; }
    .btn-hapus { background: #f44336; }
    nav a { margin-right: 15px; text-decoration: none; color: #333; font-weight: bold; }
</style>
</head>
<body>

<nav style="display:flex; justify-content:space-between; align-items:center;">
    <div>
        <a href="sampah.php">Sampah</a>
        <a href="users.php">Users</a>
        <a href="transaksi.php">Transaksi</a>
        <a href="tarik_saldo.php">Tarik Saldo</a>
    </div>
    <div>
        <span style="margin-right:10px; color:#555;">Halo, <b><?= $_SESSION['nama'] ?></b> (<?= $_SESSION['role'] ?>)</span>
        <a href="logout.php" style="color:#f44336;">Logout</a>
    </div>
</nav>
<hr>

<h2>Menu Sampah</h2>

<div class="container">

    <!-- FORM TAMBAH / EDIT -->
    <form method="POST">
        <h3><?= $edit_data ? "Edit Sampah" : "Tambah Sampah" ?></h3>

        <?php if ($edit_data) { ?>
            <input type="hidden" name="id_sampah" value="<?= $edit_data['id_sampah'] ?>">
        <?php } ?>

        <label>Jenis Sampah</label>
        <input type="text" name="jenis_sampah" value="<?= $edit_data['jenis_sampah'] ?? '' ?>" required>

        <label>Harga per kg</label>
        <input type="number" step="0.01" name="harga" value="<?= $edit_data['harga'] ?? '' ?>" required>

        <label>Stok (kg)</label>
        <input type="number" step="0.01" name="stok_kg" value="<?= $edit_data['stok_kg'] ?? 0 ?>" required>

        <button type="submit" class="btn-simpan"><?= $edit_data ? "Update" : "Simpan" ?></button>
        <?php if ($edit_data) { ?>
            <a href="sampah.php" class="btn-batal">Batal</a>
        <?php } ?>
    </form>

    <!-- TABEL DATA -->
    <table>
        <tr>
            <th>ID</th>
            <th>Jenis Sampah</th>
            <th>Harga</th>
            <th>Stok (kg)</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($data)) { ?>
        <tr>
            <td><?= $row['id_sampah'] ?></td>
            <td><?= htmlspecialchars($row['jenis_sampah']) ?></td>
            <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
            <td><?= $row['stok_kg'] ?> kg</td>
            <td>
                <a class="btn btn-edit" href="sampah.php?edit=<?= $row['id_sampah'] ?>">Edit</a>
                <a class="btn btn-hapus" href="sampah.php?hapus=<?= $row['id_sampah'] ?>"
                   onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>
