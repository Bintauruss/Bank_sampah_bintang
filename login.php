<?php
session_start();
include "koneksi.php";

// Kalau udah login, langsung lempar ke sampah.php
if (isset($_SESSION['id_user'])) {
    header("Location: sampah.php");
    exit;
}

$pesan_error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");

    if ($query && mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);

        // Simpan data ke session
        $_SESSION['id_user']  = $user['id_user'];
        $_SESSION['nama']     = $user['nama'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        header("Location: sampah.php");
        exit;
    } else {
        $pesan_error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login - Bank Sampah</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    * { box-sizing: border-box; }
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        position: relative;
        overflow: hidden;
    }
    body::before, body::after {
        content: "";
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
    }
    body::before { width: 400px; height: 400px; top: -150px; left: -150px; }
    body::after { width: 300px; height: 300px; bottom: -100px; right: -100px; }
    .login-box {
        background: rgba(255,255,255,0.97);
        padding: 40px 35px;
        border-radius: 18px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.25);
        width: 320px;
        position: relative;
        z-index: 1;
        animation: fadeUp 0.5s ease;
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .login-box .icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 15px;
        background: linear-gradient(135deg, #11998e, #38ef7d);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }
    .login-box h2 {
        text-align: center;
        color: #222;
        margin: 0 0 6px;
        font-weight: 600;
        font-size: 22px;
    }
    .login-box p.subtitle {
        text-align: center;
        color: #888;
        font-size: 13px;
        margin: 0 0 25px;
    }
    input {
        width: 100%;
        padding: 12px 14px;
        margin-bottom: 15px;
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        transition: border 0.2s;
    }
    input:focus {
        outline: none;
        border-color: #11998e;
    }
    button {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #11998e, #38ef7d);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 600;
        font-family: 'Poppins', sans-serif;
        letter-spacing: 0.5px;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    button:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(17,153,142,0.35);
    }
    .error {
        background: #ffe0e0;
        color: #c00;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 13px;
        text-align: center;
    }
</style>
</head>
<body>

<div class="login-box">
    <div class="icon">🔐</div>
    <h2>Selamat Datang</h2>
    <p class="subtitle">Login ke Bank Sampah Bintang</p>

    <?php if ($pesan_error) { ?>
        <div class="error"><?= $pesan_error ?></div>
    <?php } ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Masuk</button>
    </form>
</div>

</body>
</html>
