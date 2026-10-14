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
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f4f4;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
    }
    .login-box {
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        width: 300px;
    }
    .login-box h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }
    input {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    button {
        width: 100%;
        padding: 10px;
        background: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 15px;
    }
    button:hover {
        background: #45a049;
    }
    .error {
        background: #ffe0e0;
        color: #c00;
        padding: 8px;
        border-radius: 4px;
        margin-bottom: 15px;
        font-size: 14px;
        text-align: center;
    }
</style>
</head>
<body>

<div class="login-box">
    <h2>🔐 Login</h2>

    <?php if ($pesan_error) { ?>
        <div class="error"><?= $pesan_error ?></div>
    <?php } ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
