<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$error = "";

if (isset($_POST['login'])) {
    if ($_POST['username'] === "admin" && $_POST['password'] === "admin123") {
        $_SESSION['admin'] = "admin";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            margin: 0;
            height: 100vh;
            background: linear-gradient(135deg, #2f80ed, #27ae60);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-card {
            background: #fff;
            width: 360px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
            text-align: center;
        }
        .login-card h2 {
            margin-bottom: 25px;
            color: #333;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        input:focus {
            outline: none;
            border-color: #27ae60;
        }
        button {
            width: 100%;
            padding: 11px;
            border: none;
            border-radius: 6px;
            background: #27ae60;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #219150;
        }
        .error {
            color: red;
            margin-bottom: 12px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Admin Login</h2>

    <?php if ($error != "") { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>
