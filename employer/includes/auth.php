<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== 'employer') {
    header("Location: ../index.php");
    exit();
}

include "../connect.php";
?>