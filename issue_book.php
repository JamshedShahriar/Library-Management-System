<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin'])) exit();

if (isset($_POST['issue'])) {
    $stmt = $conn->prepare(
        "INSERT INTO Issued_Books (Book_ID, Member_ID, Issue_Date, Return_Date)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "iiss",
        $_POST['book_id'],
        $_POST['member_id'],
        $_POST['issue_date'],
        $_POST['return_date']
    );
    $stmt->execute();

    $conn->query(
        "UPDATE Books SET Available=0 WHERE Book_ID=".$_POST['book_id']
    );
}

$books = $conn->query("SELECT * FROM Books WHERE Available=1");
$members = $conn->query("SELECT * FROM Member");
?>
