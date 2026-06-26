<?php
$username = $_SESSION['username'] ?? 'Employer';
?>

<div class="topbar">

    <div class="topbar-left">
        <h4>HireConnect Employer Dashboard</h4>
    </div>

    <div class="topbar-right">

        <span class="welcome">
            Welcome, <?= htmlspecialchars($username) ?>
        </span>

        <a href="../logout.php" class="btn btn-sm btn-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>

    </div>

</div>