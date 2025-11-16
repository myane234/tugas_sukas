<?php
session_start();
require '../koneksi.php'; // file koneksi database

if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ambil user role user saja
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = 'user'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $user_data = $result->fetch_assoc();
        if(password_verify($password, $user_data['password'])){
            // login sukses
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['username'] = $user_data['username'];
            $_SESSION['role'] = $user_data['role'];
            
            header("Location: ../userPage/page_user.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login User</title>
</head>
<body>
<h2>Login User</h2>

<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="post">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="login">Login</button>
</form>

<p>Belum punya akun? <a href="register.php">Register di sini</a></p>
</body>
</html>
