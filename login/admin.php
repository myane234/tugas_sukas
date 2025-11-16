<?php
session_start();
require '../koneksi.php'; // koneksi database

// === PROSES LOGIN ADMIN ===
if (isset($_POST['login_admin'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cari user
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ditemukan?
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Cek password
        if (password_verify($password, $row['password'])) {

            // Cek role admin
            if ($row['role'] === "admin") {

                // Set session
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                // Redirect ke dashboard admin (naik folder 1)
                header("Location: ../admin/index.php");
                exit;
            } else {
                $error = "Hanya admin yang boleh login!";
            }

        } else {
            $error = "Password salah!";
        }

    } else {
        $error = "Admin tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>

    <style>
        body {
            font-family: Arial;
            background: #f3f3f3;
            padding: 40px;
        }
        .box {
            max-width: 350px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
        }
        button {
            width: 100%;
            padding: 10px;
            margin-top: 12px;
            background: #333;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover { background: #555; }
        .error { color: red; margin-top: 10px; }
        a { text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

<div class="box">
    <h3>Login Admin</h3>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username Admin" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="login_admin">Login</button>
    </form>

    <br>
    <a href="../index.php">Balik</a>
    <p>Belum punya akun? <a href="register_admin.php">Register di sini</a></p>
</div>

</body>
</html>
