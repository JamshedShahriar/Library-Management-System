<?php
session_start();
include 'config.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === "admin" && $password === "admin123") {
        $_SESSION['admin'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<style>
body {
  background: linear-gradient(135deg, #3498db, #2ecc71);
  font-family: 'Poppins', sans-serif;
}
.login {
  width: 350px;
  margin: 100px auto;
  background: white;
  border-radius: 15px;
  padding: 30px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}
.login h2 {text-align:center;color:#2c3e50;}
input,button {
  width: 100%;
  padding:10px;
  margin:10px 0;
  border-radius:8px;
  border:1px solid #ccc;
}
button {
  background:#27ae60;
  color:white;
  border:none;
  font-weight:600;
}
button:hover {background:#2ecc71;}
.error{text-align:center;color:red;}
</style>
</head>
<body>
<div class="login">
<h2>Admin Login</h2>
<?php if(!empty($error)) echo "<p class='error'>$error</p>";?>
<form method="post">
<input name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button name="login">Login</button>
</form>
</div>
</body>
</html>
