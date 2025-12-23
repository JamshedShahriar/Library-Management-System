<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle Book Add
if (isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $price = $_POST['price'];
    $conn->query("INSERT INTO Books (Title, Author, Price, Available, Pub_ID) VALUES ('$title', '$author', '$price', 1, '$publisher')");
}

// Handle Member Add
if (isset($_POST['add_member'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $conn->query("INSERT INTO Member (Name, Address) VALUES ('$name', '$address')");
}

// Handle Issue Book
if (isset($_POST['issue_book'])) {
    $book_id = $_POST['book_id'];
    $member_id = $_POST['member_id'];
    $issue_date = $_POST['issue_date'];
    $return_date = $_POST['return_date'];
    $conn->query("INSERT INTO Issued_Books (Book_ID, Member_ID, Issue_Date, Return_Date) VALUES ('$book_id','$member_id','$issue_date','$return_date')");
    $conn->query("UPDATE Books SET Available=0 WHERE Book_ID='$book_id'");
}

// Handle Return Book
if (isset($_POST['return_book'])) {
    $book_id = $_POST['book_id_return'];
    $conn->query("DELETE FROM Issued_Books WHERE Book_ID='$book_id'");
    $conn->query("UPDATE Books SET Available=1 WHERE Book_ID='$book_id'");
}

// Fetch Data
$books = $conn->query("SELECT b.Book_ID, b.Title, b.Author, b.Price, b.Available, p.Pub_Name 
                        FROM Books b JOIN Publisher p ON b.Pub_ID = p.Pub_ID");
$members = $conn->query("SELECT * FROM Member");
$issued = $conn->query("SELECT ib.Issue_ID, b.Title, m.Name, ib.Issue_Date, ib.Return_Date
                        FROM Issued_Books ib
                        JOIN Books b ON ib.Book_ID = b.Book_ID
                        JOIN Member m ON ib.Member_ID = m.Member_ID");
$publishers = $conn->query("SELECT * FROM Publisher");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Library Management System</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('library_banner.jpg.jpg') no-repeat center center fixed;
    background-size: cover;
    color: #fff;
}
header {
    background: rgba(0,0,0,0.7);
    padding: 20px;
    text-align: center;
    position: relative;
}
header h1 { margin:0; }
.logout {
    position: absolute;
    right: 20px;
    top: 25px;
    background:#ff4d4d;
    color:white;
    padding:8px 12px;
    border-radius:5px;
    text-decoration:none;
}
.logout:hover { background:#e60000; }
nav {
    display:flex;
    justify-content:center;
    gap:15px;
    background: rgba(0,0,0,0.7);
    flex-wrap: wrap;
}
nav a {
    color:white;
    padding:10px 15px;
    text-decoration:none;
    border-radius:5px;
    transition:0.3s;
}
nav a.active, nav a:hover { background:#4CAF50; }
section { display:none; padding:20px; max-width:1000px; margin:auto; }
section.active { display:block; background: rgba(0,0,0,0.6); border-radius:12px; padding:20px; margin-top:20px; box-shadow:0 0 20px rgba(0,0,0,0.5); }
form input, form select {
    width:100%;
    padding:10px;
    margin:5px 0 15px;
    border-radius:5px;
    border:none;
}
form input[type=submit] {
    background:#4CAF50;
    color:white;
    border:none;
    cursor:pointer;
    font-weight:bold;
}
form input[type=submit]:hover { background:#45a049; }
table { width:100%; border-collapse:collapse; margin-top:20px; background:#fff; color:#000; border-radius:10px; overflow:hidden; }
th, td { padding:12px; text-align:center; border-bottom:1px solid #ddd; }
th { background:#4CAF50; color:white; }
tr:hover { background:#f1f1f1; }
.available { color:green; font-weight:bold; }
.unavailable { color:red; font-weight:bold; }
.card-container { display:flex; flex-wrap:wrap; gap:20px; justify-content:center; margin-top:20px; }
.card {
    background: rgba(255,255,255,0.1);
    padding:30px;
    border-radius:12px;
    width:180px;
    text-align:center;
    transition: transform 0.3s;
}
.card:hover { transform: scale(1.05); }
.card a { color:white; text-decoration:none; font-weight:bold; display:block; margin-top:10px; }
@media (max-width:600px){
    nav { flex-direction: column; }
    .card-container { flex-direction: column; align-items:center; }
}
</style>
</head>
<body>

<header>
<h1>📚 Library Management System</h1>
<a class="logout" href="logout.php">Logout</a>
</header>

<nav>
<a href="#" class="tab-link active" data-tab="dashboard"><i class="fas fa-home"></i> Dashboard</a>
<a href="#" class="tab-link" data-tab="books"><i class="fas fa-book"></i> Add/View Books</a>
<a href="#" class="tab-link" data-tab="members"><i class="fas fa-users"></i> Register Member</a>
<a href="#" class="tab-link" data-tab="issue"><i class="fas fa-hand-paper"></i> Issue Book</a>
<a href="#" class="tab-link" data-tab="return"><i class="fas fa-undo"></i> Return Book</a>
<a href="#" class="tab-link" data-tab="issued"><i class="fas fa-list"></i> Issued Books</a>
</nav>


<section id="dashboard" class="active">
<div style="display:flex; justify-content:center; align-items:center; height:70vh; flex-direction:column; text-align:center;">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['admin']); ?> 👋</h2>
    <p>Select an option from the menu above to manage the library.</p>
</div>

<div class="card-container">
    <div class="card"><i class="fas fa-book fa-2x"></i><a href="#" onclick="openTab('books')">Books</a></div>
    <div class="card"><i class="fas fa-users fa-2x"></i><a href="#" onclick="openTab('members')">Members</a></div>
    <div class="card"><i class="fas fa-hand-paper fa-2x"></i><a href="#" onclick="openTab('issue')">Issue Book</a></div>
    <div class="card"><i class="fas fa-undo fa-2x"></i><a href="#" onclick="openTab('return')">Return Book</a></div>
    <div class="card"><i class="fas fa-list fa-2x"></i><a href="#" onclick="openTab('issued')">Issued Books</a></div>
</div>
</section>

<!-- BOOKS -->
<section id="books">
<h2>Add Book</h2>
<form method="POST">
<input type="text" name="title" placeholder="Title" required>
<input type="text" name="author" placeholder="Author" required>
<select name="publisher" required>
<option value="">Select Publisher</option>
<?php while($p = $publishers->fetch_assoc()) { ?>
<option value="<?= $p['Pub_ID']; ?>"><?= htmlspecialchars($p['Pub_Name']); ?></option>
<?php } ?>
</select>
<input type="text" name="price" placeholder="Price" required>
<input type="submit" name="add_book" value="Add Book">
</form>

<h2>All Books</h2>
<table>
<tr><th>ID</th><th>Title</th><th>Author</th><th>Publisher</th><th>Price</th><th>Available</th></tr>
<?php while($b = $books->fetch_assoc()) { ?>
<tr>
<td><?= $b['Book_ID']; ?></td>
<td><?= htmlspecialchars($b['Title']); ?></td>
<td><?= htmlspecialchars($b['Author']); ?></td>
<td><?= htmlspecialchars($b['Pub_Name']); ?></td>
<td><?= $b['Price']; ?></td>
<td class="<?= $b['Available'] ? 'available' : 'unavailable'; ?>"><?= $b['Available'] ? 'Yes' : 'No'; ?></td>
</tr>
<?php } ?>
</table>
</section>


<section id="members">
<h2>Register Member</h2>
<form method="POST">
<input type="text" name="name" placeholder="Name" required>
<input type="text" name="address" placeholder="Address" required>
<input type="submit" name="add_member" value="Register Member">
</form>

<h2>All Members</h2>
<table>
<tr><th>ID</th><th>Name</th><th>Address</th></tr>
<?php 
$members_list = $conn->query("SELECT * FROM Member");
while($m = $members_list->fetch_assoc()) { ?>
<tr>
<td><?= $m['Member_ID']; ?></td>
<td><?= htmlspecialchars($m['Name']); ?></td>
<td><?= htmlspecialchars($m['Address']); ?></td>
</tr>
<?php } ?>
</table>
</section>

<!-- ISSUE BOOK -->
<section id="issue">
<h2>Issue Book</h2>
<form method="POST">
<select name="book_id" required>
<option value="">Select Book</option>
<?php 
$available_books = $conn->query("SELECT * FROM Books WHERE Available=1");
while($ab = $available_books->fetch_assoc()) { ?>
<option value="<?= $ab['Book_ID']; ?>"><?= htmlspecialchars($ab['Title']); ?></option>
<?php } ?>
</select>
<select name="member_id" required>
<option value="">Select Member</option>
<?php 
$members_list2 = $conn->query("SELECT * FROM Member");
while($m2 = $members_list2->fetch_assoc()) { ?>
<option value="<?= $m2['Member_ID']; ?>"><?= htmlspecialchars($m2['Name']); ?></option>
<?php } ?>
</select>
<input type="date" name="issue_date" required>
<input type="date" name="return_date" required>
<input type="submit" name="issue_book" value="Issue Book">
</form>
</section>


<section id="return">
<h2>Return Book</h2>
<form method="POST">
<select name="book_id_return" required>
<option value="">Select Issued Book</option>
<?php 
$issued_books_list = $conn->query("SELECT ib.Book_ID, b.Title 
FROM Issued_Books ib JOIN Books b ON ib.Book_ID=b.Book_ID");
while($ib = $issued_books_list->fetch_assoc()) { ?>
<option value="<?= $ib['Book_ID']; ?>"><?= htmlspecialchars($ib['Title']); ?></option>
<?php } ?>
</select>
<input type="submit" name="return_book" value="Return Book">
</form>
</section>

<!-- ISSUED BOOKS -->
<section id="issued">
<h2>Issued Books</h2>
<table>
<tr><th>Issue ID</th><th>Book Title</th><th>Member</th><th>Issue Date</th><th>Return Date</th></tr>
<?php while($i = $issued->fetch_assoc()) { ?>
<tr>
<td><?= $i['Issue_ID']; ?></td>
<td><?= htmlspecialchars($i['Title']); ?></td>
<td><?= htmlspecialchars($i['Name']); ?></td>
<td><?= $i['Issue_Date']; ?></td>
<td><?= $i['Return_Date']; ?></td>
</tr>
<?php } ?>
</table>
</section>

<script>

const links = document.querySelectorAll('.tab-link');
const sections = document.querySelectorAll('section');
links.forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        links.forEach(l => l.classList.remove('active'));
        sections.forEach(s => s.classList.remove('active'));
        link.classList.add('active');
        document.getElementById(link.dataset.tab).classList.add('active');
    });
});
function openTab(tabId){
    links.forEach(l => l.classList.remove('active'));
    sections.forEach(s => s.classList.remove('active'));
    document.querySelector(.tab-link[data-tab='${tabId}']).classList.add('active');
    document.getElementById(tabId).classList.add('active');
}
</script>

</body>
</html>