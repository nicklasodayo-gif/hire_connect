<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">

    <div class="sidebar-header">
        <h3>Employer Panel</h3>
    </div>

    <a class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"
       href="dashboard.php">
        <i class="bi bi-speedometer2"></i>
        Dashboard
    </a>

    <a class="<?= $current_page == 'post_job.php' ? 'active' : '' ?>"
       href="post_job.php">
        <i class="bi bi-plus-circle"></i>
        Post Job
    </a>

    <a class="<?= $current_page == 'manage_jobs.php' ? 'active' : '' ?>"
       href="manage_jobs.php">
        <i class="bi bi-briefcase"></i>
        Manage Jobs
    </a>

    <a class="<?= $current_page == 'view_applicants.php' ? 'active' : '' ?>"
       href="view_applicants.php">
        <i class="bi bi-people"></i>
        Applicants
    </a>

    <a class="<?= $current_page == 'schedule_interview.php' ? 'active' : '' ?>"
       href="schedule_interview.php">
        <i class="bi bi-calendar-event"></i>
        Interviews
    </a>

    <a class="<?= $current_page == 'reports.php' ? 'active' : '' ?>"
       href="reports.php">
        <i class="bi bi-bar-chart"></i>
        Reports
    </a>

    <a class="<?= $current_page == 'company_profile.php' ? 'active' : '' ?>"
       href="company_profile.php">
        <i class="bi bi-building"></i>
        Company Profile
    </a>

    <a class="<?= $current_page == 'settings.php' ? 'active' : '' ?>"
       href="settings.php">
        <i class="bi bi-gear"></i>
        Settings
    </a>

    <a href="../logout.php">
        <i class="bi bi-box-arrow-right"></i>
        Logout
    </a>

</div>