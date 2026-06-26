<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] != "jobseeker") {
    header("Location: ../index.php");
    exit();
}

include("../connect.php");

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn,"
SELECT *
FROM users
WHERE user_id='$user_id'
");

$user = mysqli_fetch_assoc($query);
?>