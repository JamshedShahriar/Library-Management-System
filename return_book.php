<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin'])) exit();

if (isset($_POST['return'])) {
    $id = $_POST['issue_id'];

    $res = $conn->query(
        "SELECT Book_ID FROM Issued_Books WHERE Issue_ID=$id"
    );
    $row = $res->fetch_assoc();

    $conn->query("UPDATE Books SET Available=1 WHERE Book_ID=".$row['Book_ID']);
    $conn->query("DELETE FROM Issued_Books WHERE Issue_ID=$id");
}

$issued = $conn->query("SELECT * FROM Issued_Books");
?>
