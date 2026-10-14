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
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    * { box-sizing: border-box; }
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        min-height: 100vh;
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 50%, #a5d6a7 100%);
        padding-bottom: 40px;
    }
    h2 { color: #2e7d32; font-weight: 600; margin: 25px 40px 15px; }
    .container { display: flex; gap: 25px; margin: 0 40px; flex-wrap: wrap; }

    nav {
        background: linear-gradient(135deg, #11998e, #38ef7d);
        padding: 16px 40px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    nav a {
        margin-right: 20px;
        text-decoration: none;
        color: #fff;
        font-weight: 500;
        font-size: 14px;
        padding: 6px 12px;
        border-radius: 6px;
        transition: background 0.2s;
    }
    nav a:hover { background: rgba(255,255,255,0.2); }
    nav span { color: #fff !important; }
    nav b { color: #fff; }

    form {
        background: #fff;
        padding: 24px;
        border-radius: 14px;
        width: 300px;
        height: fit-content;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.03);
    }
    form h3 { margin-top: 0; color: #2e7d32; font-size: 17px; }
    form label { font-size: 13px; color: #666; font-weight: 500; }
    input, select {
        width: 100%;
        padding: 10px 12px;
        margin: 6px 0 14px;
        box-sizing: border-box;
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        transition: border 0.2s;
    }
    input:focus, select:focus { outline: none; border-color: #38ef7d; }

    button {
        padding: 11px 18px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        color: white;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 13px;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    button:hover { transform: translateY(-1px); box-shadow: 0 6px 15px rgba(0,0,0,0.15); }
    .btn-simpan { background: linear-gradient(135deg, #11998e, #38ef7d); }
    .btn-batal {
        background: #9e9e9e;
        text-decoration: none;
        padding: 11px 18px;
        border-radius: 8px;
        display: inline-block;
        margin-left: 6px;
        color: white;
        font-size: 13px;
        font-weight: 600;
    }

    table {
        flex: 1;
        min-width: 300px;
        border-collapse: collapse;
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    th, td { padding: 12px 14px; text-align: left; font-size: 13px; }
    th {
        background: linear-gradient(135deg, #11998e, #38ef7d);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
    }
    td { border-bottom: 1px solid #f0f0f0; color: #444; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #f7fdf8; }

    .btn { padding: 6px 12px; text-decoration: none; border-radius: 6px; color: white; font-size: 12px; font-weight: 500; margin-right: 4px; }
    .btn-edit { background: #2196F3; }
    .btn-hapus { background: #f44336; }

    .status-pending { color: #ff9800; font-weight: 600; }
    .status-sukses { color: #4CAF50; font-weight: 600; }
    .status-ditolak { color: #f44336; font-weight: 600; }
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
        <span style="margin-right:14px; font-size:13px;">👋 Halo, <b><?= $_SESSION['nama'] ?></b> <span style="opacity:0.75;">(<?= $_SESSION['role'] ?>)</span></span>
        <a href="logout.php" style="background:rgba(255,255,255,0.25); padding:6px 14px; border-radius:20px;">Logout</a>
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
 

<!-- FLOATING VIDEO WIDGET -->
<div id="videoWidget" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 220px;
    border-radius: 14px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    overflow: hidden;
    z-index: 999;
">
    <span onclick="document.getElementById('videoWidget').style.display='none'" style="
        position: absolute;
        top: 6px;
        right: 8px;
        color: #fff;
        background: rgba(0,0,0,0.4);
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 13px;
        line-height: 1;
        z-index: 1;
    ">&times;</span>
    <video autoplay muted loop playsinline style="width:100%; display:block;">
        <source src="bg.mp4" type="video/mp4">
        Browser kamu tidak mendukung video.
    </video>
</div>

</body>
</html>
