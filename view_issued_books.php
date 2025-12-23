<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin'])) exit();

$issued = $conn->query("
SELECT i.Issue_ID, b.Title, m.Name, i.Issue_Date, i.Return_Date
FROM Issued_Books i
JOIN Books b ON i.Book_ID=b.Book_ID
JOIN Member m ON i.Member_ID=m.Member_ID
");
?>
