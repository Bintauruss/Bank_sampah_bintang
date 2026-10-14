<?php
include "cek_login.php";
include "koneksi.php";

$id_login   = $_SESSION['id_user'];
$role_login = $_SESSION['role'];

if (isset($_POST['pengajuan'])) {
    $jumlah = floatval($_POST['jumlah']);
    $jenis  = mysqli_real_escape_string($koneksi, $_POST['jenis']);

    if ($jumlah <= 0) {
        echo "<script>alert('Jumlah harus lebih besar dari 0!'); window.location='tarik_saldo.php';</script>";
        exit;
    }

    if ($jenis == 'tarik') {
        $q_saldo = mysqli_query($koneksi, "SELECT saldo FROM users WHERE id_user='$id_login'");
        $u_data  = mysqli_fetch_assoc($q_saldo);
        if ($u_data['saldo'] < $jumlah) {
            echo "<script>alert('Saldo tidak mencukupi untuk melakukan penarikan!'); window.location='tarik_saldo.php';</script>";
            exit;
        }
    }

    // Menggunakan kolom 'nominal' (jika di phpMyAdmin kolomnya bernama nominal)
    $q_insert = "INSERT INTO tarik_saldo (id_user, nominal, jenis, status) VALUES ('$id_login', '$jumlah', '$jenis', 'pending')";
    
    if (mysqli_query($koneksi, $q_insert)) {
        echo "<script>alert('Pengajuan $jenis berhasil dikirim!'); window.location='tarik_saldo.php';</script>";
    } else {
        // Tampilkan error spesifik MySQL agar tahu letak kesalahannya jika masih gagal
        die("Gagal mengajukan! Error MySQL: " . mysqli_error($koneksi));
    }
    exit;
}

if (isset($_GET['aksi']) && $role_login == 'admin') {
    $id_tarik = mysqli_real_escape_string($koneksi, $_GET['id']);
    $aksi     = mysqli_real_escape_string($koneksi, $_GET['aksi']);

    $q_detail = mysqli_query($koneksi, "SELECT * FROM tarik_saldo WHERE id_tarik='$id_tarik' AND status='pending'");
    $detail   = mysqli_fetch_assoc($q_detail);

    if ($detail) {
        $user_id = $detail['id_user'];
        // Mengambil data dari kolom 'nominal'
        $jumlah  = isset($detail['nominal']) ? $detail['nominal'] : $detail['jumlah'];
        $jenis   = $detail['jenis'];

        if ($aksi == 'setuju') {
            if ($jenis == 'topup') {
                // Top Up: Tambah saldo user
                mysqli_query($koneksi, "UPDATE users SET saldo = saldo + $jumlah WHERE id_user='$user_id'");
            } else if ($jenis == 'tarik') {
                // Tarik Saldo: Kurangi saldo user
                mysqli_query($koneksi, "UPDATE users SET saldo = saldo - $jumlah WHERE id_user='$user_id'");
            }
            mysqli_query($koneksi, "UPDATE tarik_saldo SET status='disetujui' WHERE id_tarik='$id_tarik'");
            echo "<script>alert('Pengajuan berhasil disetujui!'); window.location='tarik_saldo.php';</script>";
        } else if ($aksi == 'tolak') {
            mysqli_query($koneksi, "UPDATE tarik_saldo SET status='ditolak' WHERE id_tarik='$id_tarik'");
            echo "<script>alert('Pengajuan berhasil ditolak!'); window.location='tarik_saldo.php';</script>";
        }
    } else {
        echo "<script>alert('Data pengajuan tidak ditemukan atau sudah diproses!'); window.location='tarik_saldo.php';</script>";
    }
    exit;
}

if ($role_login == 'admin') {
    $query = "SELECT t.*, u.nama, u.role FROM tarik_saldo t JOIN users u ON t.id_user = u.id_user ORDER BY t.id_tarik DESC";
} else {
    $query = "SELECT t.*, u.nama, u.role FROM tarik_saldo t JOIN users u ON t.id_user = u.id_user WHERE t.id_user='$id_login' ORDER BY t.id_tarik DESC";
}
$data = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Menu Saldo & Top Up</title>
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

    form {
        background: #fff;
        padding: 24px;
        border-radius: 14px;
        width: 320px;
        height: fit-content;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    form h3 { margin-top: 0; color: #2e7d32; font-size: 17px; }
    form label { font-size: 13px; color: #666; font-weight: 500; }
    input, select {
        width: 100%;
        padding: 10px 12px;
        margin: 6px 0 14px;
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
    }

    button {
        width: 100%;
        padding: 11px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        color: white;
        background: linear-gradient(135deg, #11998e, #38ef7d);
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 13px;
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
        font-size: 11px;
        text-transform: uppercase;
    }
    td { border-bottom: 1px solid #f0f0f0; color: #444; }

    .badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    .badge-topup { background: #e3f2fd; color: #1976d2; }
    .badge-tarik { background: #fff3e0; color: #e65100; }
    .badge-pending { background: #fff8e1; color: #f57f17; }
    .badge-disetujui { background: #e8f5e9; color: #2e7d32; }
    .badge-ditolak { background: #ffebee; color: #c62828; }

    .btn-acc { background: #4caf50; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px; }
    .btn-reject { background: #f44336; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px; }
</style>
</head>
<body>

<nav style="display:flex; justify-content:space-between; align-items:center;">
    <div>
        <?php if ($_SESSION['role'] == 'admin') { ?>
            <a href="sampah.php">Sampah</a>
            <a href="users.php">Users</a>
        <?php } ?>
        <a href="transaksi.php">Transaksi</a>
        <a href="tarik_saldo.php">Top Up / Tarik Saldo</a>
    </div>
    <div>
        <span style="color:#fff; margin-right:14px; font-size:13px;">👋 Halo, <b><?= $_SESSION['nama'] ?></b> (<?= ucfirst($_SESSION['role']) ?>)</span>
        <a href="logout.php" style="background:rgba(255,255,255,0.25); padding:6px 14px; border-radius:20px;">Logout</a>
    </div>
</nav>

<h2>Menu Top Up / Tarik Saldo</h2>

<div class="container">

    <form method="POST">
        <h3>Form Transaksi Saldo</h3>

        <label>Jenis Transaksi</label>
        <select name="jenis" required>
            <option value="topup">Top Up Saldo</option>
            <option value="tarik">Tarik Saldo</option>
        </select>

        <label>Jumlah Nominal (Rp)</label>
        <input type="number" name="jumlah" placeholder="Contoh: 50000" min="1000" required>

        <button type="submit" name="pengajuan">Kirim Pengajuan</button>
    </form>

    <!-- TABEL RIWAYAT / PERMINTAAN -->
    <table>
        <tr>
            <th>ID</th>
            <th>Nama User</th>
            <th>Role</th>
            <th>Jenis</th>
            <th>Jumlah Nominal</th>
            <th>Status</th>
            <?php if ($role_login == 'admin') { ?><th>Aksi Admin</th><?php } ?>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($data)) { ?>
        <tr>
            <td><?= $row['id_tarik'] ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= ucfirst($row['role']) ?></td>
            <td>
                <span class="badge <?= $row['jenis'] == 'topup' ? 'badge-topup' : 'badge-tarik' ?>">
                    <?= strtoupper($row['jenis']) ?>
                </span>
            </td>
           <td>Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
            <td>
                <span class="badge badge-<?= $row['status'] ?>">
                    <?= ucfirst($row['status']) ?>
                </span>
            </td>
            <?php if ($role_login == 'admin') { ?>
            <td>
                <?php if ($row['status'] == 'pending') { ?>
                    <a href="tarik_saldo.php?id=<?= $row['id_tarik'] ?>&aksi=setuju" class="btn-acc" onclick="return confirm('Setujui transaksi ini?')">Setujui</a>
                    <a href="tarik_saldo.php?id=<?= $row['id_tarik'] ?>&aksi=tolak" class="btn-reject" onclick="return confirm('Tolak transaksi ini?')">Tolak</a>
                <?php } else { echo "-"; } ?>
            </td>
            <?php } ?>
        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>