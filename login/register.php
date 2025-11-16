<?php

session_start();
require '../koneksi.php'; // file koneksi database

if(isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if($stmt->num_rows > 0) {
        $error = "username udah ada";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
        $stmt-> bind_param("ss", $username, $password);

        if($stmt->execute()) {
            header("Location : user.php");
            exit;
        } else {
            $error = "gagal register";
        }
    }
}