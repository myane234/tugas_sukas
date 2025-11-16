<?php
session_start();
require '../koneksi.php';

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Cek username kosong (extra proteksi)
    if ($username === "" || $password === "") {
        $error = "Username dan password wajib diisi.";
    } else {

        // 1. CEK apakah username sudah terdaftar
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result(); // supaya bisa ambil num_rows

        if ($check->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {

            // 2. HASH password
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            // 3. INSERT user baru
            $insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
            $insert->bind_param("ss", $username, $hashed);

            if ($insert->execute()) {
                // 4. Redirect ke login/user page
                header("Location: user.php");
                exit;
            } else {
                $error = "Gagal register. Coba lagi.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register User</title>
    <style>
        body {
            font-family: Arial;
            background: #f1f1f1;
            padding: 30px;
        }
        .container {
            width: 300px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input {
            width: 100%;
            padding: 8px;
            margin: 6px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            width: 100%;
            padding: 8px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover { opacity: 0.9; }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Register</h2>

    <?php if(isset($error)) { ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <form action="" method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" name="register">Register</button>
    </form>
</div>

</body>
</html>
