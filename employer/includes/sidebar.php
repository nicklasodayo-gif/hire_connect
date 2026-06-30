<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">

    <div class="sidebar-header">
        <h3>Employer Panel</h3>
    </div>

    <a class="<?= $current_page == 'employer\employer_dashboard.php' ? 'active' : '' ?>"
       href="employer\employer_dashboard.php">
        <i class="bi bi-speedometer2"></i>
        Dashboard
    </a>

    <a class="<?= $current_page == 'employer\post_job.php' ? 'active' : '' ?>"
       href="employer\post_job.php">
        <i class="bi bi-plus-circle"></i>
        Post Job
    </a>

    <a class="<?= $current_page == 'employer\manage_job.php' ? 'active' : '' ?>"
       href="employer\manage_job.php">
        <i class="bi bi-briefcase"></i>
        Manage Jobs
    </a>

    <a class="<?= $current_page == 'employer\view_applicants.php' ? 'active' : '' ?>"
       href="employer\view_applicants.php">
        <i class="bi bi-people"></i>
        Applicants
    </a>

    <a class="<?= $current_page == 'employer\schedule_interviews.php' ? 'active' : '' ?>"
       href="employer\schedule_interviews.php">
        <i class="bi bi-calendar-event"></i>
        Interviews
    </a>

    <a class="<?= $current_page == '#' ? 'active' : '' ?>"
       href="#">
        <i class="bi bi-bar-chart"></i>
        Reports
    </a>

    <a class="<?= $current_page == 'employer\employer_profile.php' ? 'active' : '' ?>"
       href="employer\employer_profile.php">
        <i class="bi bi-building"></i>
        Company Profile
    </a>

    <a class="<?= $current_page == 'employer\employer_settings.php' ? 'active' : '' ?>"
       href="employer\employer_settings.php">
        <i class="bi bi-gear"></i>
        Settings
    </a>

    <a href="../logout.php">
        <i class="bi bi-box-arrow-right"></i>
        Logout
    </a>

</div>