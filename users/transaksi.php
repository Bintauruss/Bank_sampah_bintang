<?php
include "cek_login.php";
include "koneksi.php";

$edit_data = null;

// ==== HAPUS ====
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM transaksi WHERE id_transaksi='$id'");
    header("Location: transaksi.php");
    exit;
}

// ==== TAMBAH / UPDATE ====
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user   = mysqli_real_escape_string($koneksi, $_POST['id_user']);
    $id_sampah = mysqli_real_escape_string($koneksi, $_POST['id_sampah']);
    $tipe      = mysqli_real_escape_string($koneksi, $_POST['tipe']);
    $berat_kg  = mysqli_real_escape_string($koneksi, $_POST['berat_kg']);
    $total_rp  = mysqli_real_escape_string($koneksi, $_POST['total_rp']);

    if (!empty($_POST['id_transaksi'])) {
        // UPDATE
        $id = mysqli_real_escape_string($koneksi, $_POST['id_transaksi']);
        mysqli_query($koneksi, "UPDATE transaksi SET id_user='$id_user', id_sampah='$id_sampah', tipe='$tipe', berat_kg='$berat_kg', total_rp='$total_rp' WHERE id_transaksi='$id'");
    } else {
        // INSERT
        mysqli_query($koneksi, "INSERT INTO transaksi (id_user, id_sampah, tipe, berat_kg, total_rp) VALUES ('$id_user','$id_sampah','$tipe','$berat_kg','$total_rp')");
    }
    header("Location: transaksi.php");
    exit;
}

// ==== AMBIL DATA UNTUK EDIT ====
if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $result = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id_transaksi='$id'");
    $edit_data = mysqli_fetch_assoc($result);
}

// ==== DATA UNTUK DROPDOWN ====
$users_list  = mysqli_query($koneksi, "SELECT id_user, nama FROM users");
$sampah_list = mysqli_query($koneksi, "SELECT id_sampah, jenis_sampah FROM sampah");

// ==== AMBIL SEMUA DATA TRANSAKSI (JOIN biar keliatan nama & jenis sampah) ====
$data = mysqli_query($koneksi, "
    SELECT t.*, u.nama, s.jenis_sampah
    FROM transaksi t
    JOIN users u ON t.id_user = u.id_user
    JOIN sampah s ON t.id_sampah = s.id_sampah
    ORDER BY t.id_transaksi DESC
");
if (!$data) {
    die("Query gagal: " . mysqli_error($koneksi) . " — cek apakah tabel 'transaksi', 'users', 'sampah' sudah ada di database '$db'.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Menu Transaksi</title>
<style>
    body { font-family: Arial, sans-serif; margin: 40px; background: #f4f4f4; }
    h2 { color: #333; }
    .container { display: flex; gap: 30px; }
    form { background: #fff; padding: 20px; border-radius: 6px; width: 300px; height: fit-content; }
    input, select { width: 100%; padding: 8px; margin-bottom: 12px; box-sizing: border-box; }
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

<h2>Menu Transaksi</h2>

<div class="container">

    <!-- FORM TAMBAH / EDIT -->
    <form method="POST">
        <h3><?= $edit_data ? "Edit Transaksi" : "Tambah Transaksi" ?></h3>

        <?php if ($edit_data) { ?>
            <input type="hidden" name="id_transaksi" value="<?= $edit_data['id_transaksi'] ?>">
        <?php } ?>

        <label>User</label>
        <select name="id_user" required>
            <option value="">-- Pilih User --</option>
            <?php while ($u = mysqli_fetch_assoc($users_list)) { ?>
                <option value="<?= $u['id_user'] ?>" <?= (isset($edit_data['id_user']) && $edit_data['id_user']==$u['id_user']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($u['nama']) ?>
                </option>
            <?php } ?>
        </select>

        <label>Jenis Sampah</label>
        <select name="id_sampah" required>
            <option value="">-- Pilih Sampah --</option>
            <?php while ($s = mysqli_fetch_assoc($sampah_list)) { ?>
                <option value="<?= $s['id_sampah'] ?>" <?= (isset($edit_data['id_sampah']) && $edit_data['id_sampah']==$s['id_sampah']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['jenis_sampah']) ?>
                </option>
            <?php } ?>
        </select>

        <label>Tipe</label>
        <input type="text" name="tipe" placeholder="setor / jual" value="<?= $edit_data['tipe'] ?? '' ?>" required>

        <label>Berat (kg)</label>
        <input type="number" step="0.01" name="berat_kg" value="<?= $edit_data['berat_kg'] ?? '' ?>" required>

        <label>Total (Rp)</label>
        <input type="number" step="0.01" name="total_rp" value="<?= $edit_data['total_rp'] ?? '' ?>" required>

        <button type="submit" class="btn-simpan"><?= $edit_data ? "Update" : "Simpan" ?></button>
        <?php if ($edit_data) { ?>
            <a href="transaksi.php" class="btn-batal">Batal</a>
        <?php } ?>
    </form>

    <!-- TABEL DATA -->
    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Sampah</th>
            <th>Tipe</th>
            <th>Berat (kg)</th>
            <th>Total</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($data)) { ?>
        <tr>
            <td><?= $row['id_transaksi'] ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['jenis_sampah']) ?></td>
            <td><?= $row['tipe'] ?></td>
            <td><?= $row['berat_kg'] ?> kg</td>
            <td>Rp <?= number_format($row['total_rp'], 0, ',', '.') ?></td>
            <td><?= $row['tanggal'] ?></td>
            <td>
                <a class="btn btn-edit" href="transaksi.php?edit=<?= $row['id_transaksi'] ?>">Edit</a>
                <a class="btn btn-hapus" href="transaksi.php?hapus=<?= $row['id_transaksi'] ?>"
                   onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>
