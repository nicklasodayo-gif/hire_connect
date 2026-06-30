<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<head>

<meta charset="UTF-8">

<title>Dashboard</title>

<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<link rel="stylesheet" href="includes/sidebar.css">

</head>

<div class="sidebar">

    <div class="logo">
        <i class="bi bi-briefcase-fill"></i>
        <span>HireConnect</span>
    </div>

    <ul>

        <li>
            <a href="dashboard.php"
               class="<?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
        </li>

        <li>
            <a href="browse_jobs.php"
               class="<?= ($current_page == 'browse_jobs.php') ? 'active' : ''; ?>">
                <i class="bi bi-search"></i>
                Browse Jobs
            </a>
        </li>

        <li>
            <a href="saved_jobs.php"
               class="<?= ($current_page == 'saved_jobs.php') ? 'active' : ''; ?>">
                <i class="bi bi-bookmark-heart"></i>
                Saved Jobs
            </a>
        </li>

        <li>
            <a href="my_applications.php"
               class="<?= ($current_page == 'my_applications.php') ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-text"></i>
                My Applications
            </a>
        </li>

        <li>
            <a href="profile.php"
               class="<?= ($current_page == 'profile.php') ? 'active' : ''; ?>">
                <i class="bi bi-person-circle"></i>
                Profile
            </a>
        </li>

        <li>
            <a href="settings.php"
               class="<?= ($current_page == 'settings.php') ? 'active' : ''; ?>">
                <i class="bi bi-gear"></i>
                Settings
            </a>
        </li>

    </ul>

    <div class="logout">
        <a href="../logout.php">
            <i class="bi bi-box-arrow-right"></i>
            Logout
        </a>
    </div>

</div>