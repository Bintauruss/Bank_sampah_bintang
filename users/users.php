<?php
include "cek_login.php";
include "koneksi.php";

$edit_data = null;

// ==== HAPUS ====
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM users WHERE id_user='$id'");
    header("Location: users.php");
    exit;
}

// ==== TAMBAH / UPDATE ====
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $saldo    = mysqli_real_escape_string($koneksi, $_POST['saldo']);
    $role     = mysqli_real_escape_string($koneksi, $_POST['role']);

    if (!empty($_POST['id_user'])) {
        // UPDATE
        $id = mysqli_real_escape_string($koneksi, $_POST['id_user']);
        mysqli_query($koneksi, "UPDATE users SET nama='$nama', username='$username', password='$password', saldo='$saldo', role='$role' WHERE id_user='$id'");
    } else {
        // INSERT
        mysqli_query($koneksi, "INSERT INTO users (nama, username, password, saldo, role) VALUES ('$nama','$username','$password','$saldo','$role')");
    }
    header("Location: users.php");
    exit;
}

// ==== AMBIL DATA UNTUK EDIT ====
if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $result = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user='$id'");
    $edit_data = mysqli_fetch_assoc($result);
}

// ==== AMBIL SEMUA DATA ====
$data = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id_user DESC");
if (!$data) {
    die("Query gagal: " . mysqli_error($koneksi) . " — cek apakah tabel 'users' sudah ada di database '$db'.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Menu Users</title>
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

<h2>Menu Users</h2>

<div class="container">

    <!-- FORM TAMBAH / EDIT -->
    <form method="POST">
        <h3><?= $edit_data ? "Edit User" : "Tambah User" ?></h3>

        <?php if ($edit_data) { ?>
            <input type="hidden" name="id_user" value="<?= $edit_data['id_user'] ?>">
        <?php } ?>

        <label>Nama</label>
        <input type="text" name="nama" value="<?= $edit_data['nama'] ?? '' ?>" required>

        <label>Username</label>
        <input type="text" name="username" value="<?= $edit_data['username'] ?? '' ?>" required>

        <label>Password</label>
        <input type="text" name="password" value="<?= $edit_data['password'] ?? '' ?>" required>

        <label>Saldo</label>
        <input type="number" step="0.01" name="saldo" value="<?= $edit_data['saldo'] ?? 0 ?>" required>

        <label>Role</label>
        <select name="role">
            <option value="user" <?= (isset($edit_data['role']) && $edit_data['role']=='user') ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= (isset($edit_data['role']) && $edit_data['role']=='admin') ? 'selected' : '' ?>>Admin</option>
        </select>

        <button type="submit" class="btn-simpan"><?= $edit_data ? "Update" : "Simpan" ?></button>
        <?php if ($edit_data) { ?>
            <a href="users.php" class="btn-batal">Batal</a>
        <?php } ?>
    </form>

    <!-- TABEL DATA -->
    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Saldo</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($data)) { ?>
        <tr>
            <td><?= $row['id_user'] ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td>Rp <?= number_format($row['saldo'], 0, ',', '.') ?></td>
            <td><?= $row['role'] ?></td>
            <td>
                <a class="btn btn-edit" href="users.php?edit=<?= $row['id_user'] ?>">Edit</a>
                <a class="btn btn-hapus" href="users.php?hapus=<?= $row['id_user'] ?>"
                   onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>
