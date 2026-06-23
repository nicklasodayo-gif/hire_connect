<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HireConnect</title>

    <link rel="stylesheet" href="assets\common.css">
    <link rel="stylesheet" href="assets\header.css">

    <?php if(isset($page_css)): ?>
        <link rel="stylesheet" href="<?php echo $page_css; ?>">
    <?php endif; ?>

</head>
<body>

<header>
    <div class="logo">
        <a href="index.php">HireConnect</a>
    </div>

    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="jobs.php">Job Listings</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        </ul>
    </nav>
</header>