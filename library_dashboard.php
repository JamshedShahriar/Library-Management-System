<?php
include 'config.php';

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $price = $_POST['price'];
    $sql = "INSERT INTO Books (Title, Author, Price, Available, Pub_ID) VALUES ('$title', '$author', '$price', 1, '$publisher')";
    $conn->query($sql);
}
?>
<!DOCTYPE html>
<html>
<head><title>Add/View Books</title></head>
<body>
<h2>Add Book</h2>
<form method="POST">
Title: <input type="text" name="title" required><br>
Author: <input type="text" name="author" required><br>
Publisher ID: <input type="number" name="publisher" required><br>
Price: <input type="text" name="price" required><br>
<input type="submit" name="submit" value="Add Book">
</form>
<h2>All Books</h2>
<table border="1" cellpadding="8">
<tr>
<th>ID</th><th>Title</th><th>Author</th><th>Publisher</th><th>Price</th><th>Available</th>
</tr>
<?php
$result = $conn->query("SELECT * FROM Books");
while ($row = $result->fetch_assoc()) {
    echo "<tr>
    <td>{$row['Book_ID']}</td>
    <td>{$row['Title']}</td>
    <td>{$row['Author']}</td>
    <td>{$row['Pub_ID']}</td>
    <td>{$row['Price']}</td>
    <td>" . ($row['Available'] ? 'Yes' : 'No') . "</td>
    </tr>";
}
?>
</table>
</body>
</html>
