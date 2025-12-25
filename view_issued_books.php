<?php
include 'config.php';

/* ---------- ISSUE BOOK (Insert logic) ---------- */
if (isset($_POST['issue_book'])) {
    $book_id = intval($_POST['book_id']);
    $member_id = intval($_POST['member_id']);
    $issue_date = $_POST['issue_date'];
    $return_date = $_POST['return_date'];

    // Validate before inserting
    if ($book_id && $member_id && $issue_date && $return_date) {
        $stmt = $conn->prepare("INSERT INTO Issued_Books (Book_ID, Member_ID, Issue_Date, Return_Date) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iiss", $book_id, $member_id, $issue_date, $return_date);
            $stmt->execute();

            if ($stmt->error) {
                echo "<p style='color:red;'>Error issuing book: " . htmlspecialchars($stmt->error) . "</p>";
            } else {
                // Update book availability
                $conn->query("UPDATE Books SET Available = 0 WHERE Book_ID = $book_id");
                echo "<p style='color:green;'>✅ Book issued successfully!</p>";
            }

            $stmt->close();
        } else {
            echo "<p style='color:red;'>Prepare failed: " . htmlspecialchars($conn->error) . "</p>";
        }
    }
}

/* ---------- FETCH DATA FOR DROPDOWNS & TABLE ---------- */
$books = $conn->query("SELECT * FROM Books WHERE Available = 1");
$members = $conn->query("SELECT * FROM Member");
$issued = $conn->query("
    SELECT ib.Issue_ID, b.Title, m.Name, ib.Issue_Date, ib.Return_Date
    FROM Issued_Books ib
    JOIN Books b ON ib.Book_ID = b.Book_ID
    JOIN Member m ON ib.Member_ID = m.Member_ID
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📦 Issue & View Issued Books</title>
<style>
body{font-family:'Poppins',sans-serif;margin:0;background:#f4f6f9;}
header{
  background:#2c3e50;color:white;text-align:center;padding:20px;
}
.section{
  max-width:900px;margin:30px auto;background:white;
  padding:25px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
input,select{
  width:90%;padding:10px;margin:8px 0;border-radius:6px;border:1px solid #ccc;
}
button{
  background:#3498db;color:white;border:none;padding:10px 18px;border-radius:6px;cursor:pointer;
}
button:hover{background:#2ecc71;}
table{width:100%;border-collapse:collapse;margin-top:15px;}
table,th,td{border:1px solid #ccc;padding:10px;text-align:center;}
th{background:#2c3e50;color:white;}
</style>
</head>
<body>

<header>
  <h1>📚 Issue & View Issued Books</h1>
</header>

<div class="section">
  <h2>📦 Issue a New Book</h2>
  <form method="post">
    <select name="book_id" required>
      <option value="">Select Book</option>
      <?php while ($b = $books->fetch_assoc()) { ?>
        <option value="<?= $b['Book_ID']; ?>"><?= htmlspecialchars($b['Title']); ?></option>
      <?php } ?>
    </select><br>
    <select name="member_id" required>
      <option value="">Select Member</option>
      <?php while ($m = $members->fetch_assoc()) { ?>
        <option value="<?= $m['Member_ID']; ?>"><?= htmlspecialchars($m['Name']); ?></option>
      <?php } ?>
    </select><br>
    <input type="date" name="issue_date" required><br>
    <input type="date" name="return_date" required><br>
    <button name="issue_book">Issue Book</button>
  </form>
</div>

<div class="section">
  <h2>📋 Issued Books List</h2>
  <table>
    <tr><th>Issue ID</th><th>Book Title</th><th>Member</th><th>Issue Date</th><th>Return Date</th></tr>
    <?php if ($issued && $issued->num_rows > 0): ?>
      <?php while ($row = $issued->fetch_assoc()): ?>
      <tr>
        <td><?= $row['Issue_ID']; ?></td>
        <td><?= htmlspecialchars($row['Title']); ?></td>
        <td><?= htmlspecialchars($row['Name']); ?></td>
        <td><?= $row['Issue_Date']; ?></td>
        <td><?= $row['Return_Date']; ?></td>
      </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="5">No issued books found.</td></tr>
    <?php endif; ?>
  </table>
</div>

</body>
</html>
