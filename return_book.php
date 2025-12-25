<?php
include 'config.php';

if (isset($_POST['return'])) {
    $book_id = $_POST['book_id'];
    $conn->query("DELETE FROM Issued_Books WHERE Book_ID = $book_id");
    $conn->query("UPDATE Books SET Available = 1 WHERE Book_ID = $book_id");
    echo "<p style='color:green; text-align:center;'>✅ Book Returned Successfully!</p>";
}
?>
<!DOCTYPE html>
<html>
<head><title>Return Book</title></head>
<body>
<h2>Return Book</h2>
<form method="POST">
Book ID: <input type="number" name="book_id" required>
<input type="submit" name="return" value="Return">
</form>
</body>
</html>
