<?php
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h1>Welcome to HireConnect</h1>

<p>Email: <?php echo $_SESSION["email"]; ?></p>

<p>Role: <?php echo $_SESSION["role"]; ?></p>

<a href="logout.php">Logout</a>

</body>
</html>