<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="includes/sidebar.css">

<div class="sidebar">

    <div class="logo">
        <i class="bi bi-briefcase-fill"></i>
        <span>HireConnect</span>
    </div>

    <ul>

        <li>
            <a href="employer_dashboard.php"
            class="<?= ($current_page=="employer_dashboard.php")?'active':'';?>">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
        </li>

        <li>
            <a href="post_job.php"
            class="<?= ($current_page=="post_job.php")?'active':'';?>">
                <i class="bi bi-plus-circle"></i>
                Post Job
            </a>
        </li>

        <li>
            <a href="manage_job.php"
            class="<?= ($current_page=="manage_jobs.php")?'active':'';?>">
                <i class="bi bi-briefcase"></i>
                Manage Jobs
            </a>
        </li>

        <li>
            <a href="view_applicants.php"
            class="<?= ($current_page=="view_applicants.php")?'active':'';?>">
                <i class="bi bi-people"></i>
                Applicants
            </a>
        </li>

        <li>
            <a href="schedule_interviews.php"
            class="<?= ($current_page=="schedule_interviews.php")?'active':'';?>">
                <i class="bi bi-calendar-event"></i>
                Interviews
            </a>
        </li>

        <li>
            <a href="employer_reports.php"
            class="<?= ($current_page=="reports.php")?'active':'';?>">
                <i class="bi bi-bar-chart"></i>
                Reports
            </a>
        </li>

        <li>
            <a href="employer_profile.php"
            class="<?= ($current_page=="employer_profile.php")?'active':'';?>">
                <i class="bi bi-building"></i>
                Company Profile
            </a>
        </li>

        <li>
            <a href="employer_settings.php"
            class="<?= ($current_page=="employer_settings.php")?'active':'';?>">
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