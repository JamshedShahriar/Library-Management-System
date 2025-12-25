<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

/* ---------- COUNTS FOR DASHBOARD ---------- */
$totalBooks     = $conn->query("SELECT COUNT(*) c FROM Books")->fetch_assoc()['c'];
$totalMembers   = $conn->query("SELECT COUNT(*) c FROM Member")->fetch_assoc()['c'];
$totalIssued    = $conn->query("SELECT COUNT(*) c FROM Issued_Books")->fetch_assoc()['c'];
$availableBooks = $conn->query("SELECT COUNT(*) c FROM Books WHERE Available=1")->fetch_assoc()['c'];

/* ---------- ADD BOOK ---------- */
if (isset($_POST['add_book'])) {
    $title  = trim($_POST['title']);
    $author = trim($_POST['author']);
    $price  = floatval($_POST['price']);
    $available = isset($_POST['available']) ? 1 : 0;

    $stmt = $conn->prepare(
      "INSERT INTO Books (Title, Author, Price, Available) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssdi", $title, $author, $price, $available);
    $stmt->execute();
    $stmt->close();
}

/* ---------- ADD MEMBER ---------- */
if (isset($_POST['add_member'])) {
    $name    = trim($_POST['name']);
    $address = trim($_POST['address']);

    $stmt = $conn->prepare(
      "INSERT INTO Member (Name, Address) VALUES (?, ?)"
    );
    $stmt->bind_param("ss", $name, $address);
    $stmt->execute();
    $stmt->close();
}

/* ---------- ISSUE BOOK ---------- */
if (isset($_POST['issue_book'])) {
    $book_id    = intval($_POST['book_id']);
    $member_id  = intval($_POST['member_id']);
    $issue_date = $_POST['issue_date'];
    $return_date= $_POST['return_date'];

    $stmt = $conn->prepare(
      "INSERT INTO Issued_Books (Book_ID, Member_ID, Issue_Date, Return_Date)
       VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("iiss", $book_id, $member_id, $issue_date, $return_date);
    $stmt->execute();
    $stmt->close();

    $conn->query("UPDATE Books SET Available=0 WHERE Book_ID=$book_id");
}

/* ---------- RETURN BOOK ---------- */
if (isset($_POST['return_book'])) {
    $issue_id = intval($_POST['issue_id']);
    $res = $conn->query(
      "SELECT Book_ID FROM Issued_Books WHERE Issue_ID=$issue_id"
    );
    if ($row = $res->fetch_assoc()) {
        $book_id = $row['Book_ID'];
        $conn->query("DELETE FROM Issued_Books WHERE Issue_ID=$issue_id");
        $conn->query("UPDATE Books SET Available=1 WHERE Book_ID=$book_id");
    }
}

/* ---------- FETCH DATA ---------- */
$books   = $conn->query("SELECT * FROM Books");
$members = $conn->query("SELECT * FROM Member");
$issued  = $conn->query(
  "SELECT i.Issue_ID, b.Title, m.Name, i.Issue_Date, i.Return_Date
   FROM Issued_Books i
   JOIN Books b ON i.Book_ID=b.Book_ID
   JOIN Member m ON i.Member_ID=m.Member_ID"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Library Dashboard</title>
<style>
html,body{
    margin:0;
    padding:0;
    height:100%;
    font-family:'Poppins',sans-serif;
}
body{
    background: url('library_banner.jpg.jpg') no-repeat center center fixed;
    background-size: cover;
    display:flex;
    flex-direction:column;
    min-height:100vh;
}
.header{
    background: rgba(0,0,0,0.6);
    color:white;
    text-align:center;
    padding:30px;
}
.navbar{
    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    background: rgba(0,0,0,0.7);
    padding:10px;
}
.navbar button{
    margin:5px;
    padding:10px 20px;
    border:none;
    border-radius:6px;
    color:white;
    background:#1abc9c;
    cursor:pointer;
    transition:.3s;
}
.navbar button:hover{background:#16a085;}
.navbar form{margin:0;}
.navbar form button{
    background:#e74c3c;
    color:white;
    border:none;
    padding:10px 20px;
    border-radius:6px;
    cursor:pointer;
}
.navbar form button:hover{background:#c0392b;}
.section{
    display:none;
    max-width:900px;
    margin:30px auto;
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
.section.active{display:block;}
.cards{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    justify-content:center;
    margin-top:20px;
}
.card{
    background:#1abc9c;
    color:white;
    padding:20px;
    border-radius:10px;
    text-align:center;
    width:160px;
    font-size:16px;
    font-weight:600;
}
input,select,button{padding:10px;margin:5px 0;border-radius:6px;border:1px solid #ccc;}
button{background:#3498db;color:white;border:none;cursor:pointer;}
button:hover{background:#2ecc71;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{border:1px solid #ccc;padding:10px;text-align:center;}
th{background:#34495e;color:white;}
footer{
    text-align:center;
    padding:20px;
    margin-top:auto;
    background: rgba(0,0,0,0.6);
    color:white;
    font-weight:bold;
}
</style>
</head>
<body>

<header class="header">
  <h1>📚 Library Management System</h1>
  <p>Welcome, <?= htmlspecialchars($_SESSION['admin']); ?> 👋</p>
</header>

<nav class="navbar">
  <button onclick="showSection('dashboard')">Dashboard</button>
  <button onclick="showSection('books')">Books</button>
  <button onclick="showSection('members')">Members</button>
  <button onclick="showSection('issue')">Issue</button>
  <button onclick="showSection('return')">Return</button>
  <button onclick="showSection('issued')">Issued</button>
  <form action="logout.php" method="post">
    <button>Logout</button>
  </form>
</nav>

<section id="dashboard" class="section active">
  <h2>Dashboard Overview</h2>
  <div class="cards">
    <div class="card">📚<br>Total Books<br><b><?= $totalBooks ?></b></div>
    <div class="card">👥<br>Total Members<br><b><?= $totalMembers ?></b></div>
    <div class="card">📦<br>Issued Books<br><b><?= $totalIssued ?></b></div>
    <div class="card">✅<br>Available Books<br><b><?= $availableBooks ?></b></div>
  </div>
</section>

<section id="books" class="section">
<h2>Add Book</h2>
<form method="post">
<input name="title" placeholder="Title" required>
<input name="author" placeholder="Author" required>
<input name="price" type="number" step="0.01" placeholder="Price">
Available: <select name="available">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
<button name="add_book">Add Book</button>
</form>

<table>
<tr><th>ID</th><th>Title</th><th>Author</th><th>Price</th><th>Available</th></tr>
<?php while($b=$books->fetch_assoc()): ?>
<tr>
<td><?= $b['Book_ID'] ?></td>
<td><?= htmlspecialchars($b['Title']) ?></td>
<td><?= htmlspecialchars($b['Author']) ?></td>
<td><?= $b['Price'] ?></td>
<td><?= $b['Available'] ? 'Yes' : 'No' ?></td>
</tr>
<?php endwhile; ?>
</table>
</section>

<section id="members" class="section">
<h2>Register Member</h2>
<form method="post">
<input name="name" placeholder="Name" required>
<input name="address" placeholder="Address" required>
<button name="add_member">Register</button>
</form>
</section>

<section id="issue" class="section">
<h2>Issue Book</h2>
<form method="post">
<select name="book_id" required>
<option value="">Select Book</option>
<?php
$ab=$conn->query("SELECT * FROM Books WHERE Available=1");
while($r=$ab->fetch_assoc()):
?>
<option value="<?= $r['Book_ID'] ?>">
<?= htmlspecialchars($r['Title']) ?>
</option>
<?php endwhile; ?>
</select>

<select name="member_id" required>
<option value="">Select Member</option>
<?php while($m=$members->fetch_assoc()): ?>
<option value="<?= $m['Member_ID'] ?>">
<?= htmlspecialchars($m['Name']) ?>
</option>
<?php endwhile; ?>
</select>

<input type="date" name="issue_date" required>
<input type="date" name="return_date" required>
<button name="issue_book">Issue</button>
</form>
</section>

<section id="return" class="section">
<h2>Return Book</h2>
<form method="post">
<select name="issue_id" required>
<option value="">Select Issued Book</option>
<?php
$ib=$conn->query(
 "SELECT i.Issue_ID,b.Title
  FROM Issued_Books i JOIN Books b ON i.Book_ID=b.Book_ID"
);
while($r=$ib->fetch_assoc()):
?>
<option value="<?= $r['Issue_ID'] ?>">
<?= htmlspecialchars($r['Title']) ?>
</option>
<?php endwhile; ?>
</select>
<button name="return_book">Return</button>
</form>
</section>

<section id="issued" class="section">
<h2>Issued Books</h2>
<table>
<tr><th>ID</th><th>Book</th><th>Member</th><th>Issue</th><th>Return</th></tr>
<?php while($i=$issued->fetch_assoc()): ?>
<tr>
<td><?= $i['Issue_ID'] ?></td>
<td><?= htmlspecialchars($i['Title']) ?></td>
<td><?= htmlspecialchars($i['Name']) ?></td>
<td><?= $i['Issue_Date'] ?></td>
<td><?= $i['Return_Date'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</section>

<footer>© 2025 Library Management System</footer>

<script>
function showSection(id){
 document.querySelectorAll('.section').forEach(s=>s.classList.remove('active'));
 document.getElementById(id).classList.add('active');
}
</script>

</body>
</html>
