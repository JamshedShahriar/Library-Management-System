<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin'])) exit();

if (isset($_POST['add_book'])) {
    $stmt = $conn->prepare(
        "INSERT INTO Books (Title, Author, Price, Available)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "ssdi",
        $_POST['title'],
        $_POST['author'],
        $_POST['price'],
        $_POST['available']
    );
    $stmt->execute();
}

$books = $conn->query("SELECT * FROM Books");
?>
