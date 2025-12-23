<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin'])) exit();

if (isset($_POST['register'])) {
    $stmt = $conn->prepare(
        "INSERT INTO Member (Name, Email, Phone) VALUES (?, ?, ?)"
    );
    $stmt->bind_param(
        "sss",
        $_POST['name'],
        $_POST['email'],
        $_POST['phone']
    );
    $stmt->execute();
}
?>
