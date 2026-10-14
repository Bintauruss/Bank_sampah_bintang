<?php
include "cek_login.php";
include "koneksi.php";

$edit_data = null;

// ==== HAPUS ====
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM tarik_saldo WHERE id_tarik='$id'");
    header("Location: tarik_saldo.php");
    exit;
}

// ==== TAMBAH / UPDATE ====
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = mysqli_real_escape_string($koneksi, $_POST['id_user']);
    $nominal = mysqli_real_escape_string($koneksi, $_POST['nominal']);
    $status  = mysqli_real_escape_string($koneksi, $_POST['status']);

    if (!empty($_POST['id_tarik'])) {
        // UPDATE
        $id = mysqli_real_escape_string($koneksi, $_POST['id_tarik']);
        mysqli_query($koneksi, "UPDATE tarik_saldo SET id_user='$id_user', nominal='$nominal', status='$status' WHERE id_tarik='$id'");
    } else {
        // INSERT
        mysqli_query($koneksi, "INSERT INTO tarik_saldo (id_user, nominal, status) VALUES ('$id_user','$nominal','$status')");
    }
    header("Location: tarik_saldo.php");
    exit;
}

// ==== AMBIL DATA UNTUK EDIT ====
if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $result = mysqli_query($koneksi, "SELECT * FROM tarik_saldo WHERE id_tarik='$id'");
    $edit_data = mysqli_fetch_assoc($result);
}

// ==== DATA UNTUK DROPDOWN ====
$users_list = mysqli_query($koneksi, "SELECT id_user, nama FROM users");

// ==== AMBIL SEMUA DATA (JOIN biar keliatan nama user) ====
$data = mysqli_query($koneksi, "
    SELECT ts.*, u.nama
    FROM tarik_saldo ts
    JOIN users u ON ts.id_user = u.id_user
    ORDER BY ts.id_tarik DESC
");
if (!$data) {
    die("Query gagal: " . mysqli_error($koneksi) . " — cek apakah tabel 'tarik_saldo' dan 'users' sudah ada di database '$db'.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Menu Tarik Saldo</title>
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
    .status-pending { color: #ff9800; font-weight: bold; }
    .status-sukses { color: #4CAF50; font-weight: bold; }
    .status-ditolak { color: #f44336; font-weight: bold; }
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

<h2>Menu Tarik Saldo</h2>

<div class="container">

    <!-- FORM TAMBAH / EDIT -->
    <form method="POST">
        <h3><?= $edit_data ? "Edit Tarik Saldo" : "Tambah Tarik Saldo" ?></h3>

        <?php if ($edit_data) { ?>
            <input type="hidden" name="id_tarik" value="<?= $edit_data['id_tarik'] ?>">
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

        <label>Nominal</label>
        <input type="number" step="0.01" name="nominal" value="<?= $edit_data['nominal'] ?? '' ?>" required>

        <label>Status</label>
        <select name="status">
            <option value="pending" <?= (isset($edit_data['status']) && $edit_data['status']=='pending') ? 'selected' : '' ?>>Pending</option>
            <option value="sukses" <?= (isset($edit_data['status']) && $edit_data['status']=='sukses') ? 'selected' : '' ?>>Sukses</option>
            <option value="ditolak" <?= (isset($edit_data['status']) && $edit_data['status']=='ditolak') ? 'selected' : '' ?>>Ditolak</option>
        </select>

        <button type="submit" class="btn-simpan"><?= $edit_data ? "Update" : "Simpan" ?></button>
        <?php if ($edit_data) { ?>
            <a href="tarik_saldo.php" class="btn-batal">Batal</a>
        <?php } ?>
    </form>

    <!-- TABEL DATA -->
    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Nominal</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($data)) { ?>
        <tr>
            <td><?= $row['id_tarik'] ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td>Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
            <td><?= $row['tanggal'] ?></td>
            <td class="status-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></td>
            <td>
                <a class="btn btn-edit" href="tarik_saldo.php?edit=<?= $row['id_tarik'] ?>">Edit</a>
                <a class="btn btn-hapus" href="tarik_saldo.php?hapus=<?= $row['id_tarik'] ?>"
                   onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>
