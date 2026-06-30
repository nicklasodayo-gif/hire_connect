<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/config.php";

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT full_name, role, profile_photo
FROM users
WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();


/* ==========================================
   USER PROFILE IMAGE
========================================== */

$profileImage = "../uploads/profiles/default.png";

if(
    !empty($user['profile_photo']) &&
    file_exists("../uploads/profiles/".$user['profile_photo'])
){

    $profileImage = "../uploads/profiles/".$user['profile_photo'];

}

/* ==========================================
   NOTIFICATIONS
========================================== */

$notificationCount = 0;

$stmt = $conn->prepare("
SELECT COUNT(*) total
FROM notifications
WHERE user_id=?
AND is_read=0
");

if($stmt){

    $stmt->bind_param("i",$user['user_id']);
    $stmt->execute();

    $notificationCount = $stmt
    ->get_result()
    ->fetch_assoc()['total'];

}

?>

<div class="topbar">

    <div class="topbar-left">

        <h4 class="mb-1">

            Welcome,

            <strong>

                <?= htmlspecialchars($user['full_name']); ?>

            </strong>

        </h4>

        <small class="text-muted">

            <?= date("l, d F Y"); ?>

        </small>

    </div>

    <div class="topbar-right d-flex align-items-center gap-3">

        <!-- Notifications -->

        <a
        href="notifications.php"
        class="position-relative text-dark text-decoration-none">

            <i class="bi bi-bell-fill fs-4"></i>

            <?php if($notificationCount>0){ ?>

                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

                    <?= $notificationCount; ?>

                </span>

            <?php } ?>

        </a>

        <!-- Profile Dropdown -->

        <div class="dropdown">

            <a
            href="#"
            class="d-flex align-items-center text-decoration-none"
            data-bs-toggle="dropdown">

                <img
                src="<?= $profileImage; ?>"
                class="profile-image rounded-circle shadow-sm">

                <div class="ms-2 d-none d-lg-block">

                    <strong>

                        <?= htmlspecialchars($user['full_name']); ?>

                    </strong>

                    <br>

                    <small class="text-muted">

                        <?= ucfirst($user['role']); ?>

                    </small>

                </div>

                <i class="bi bi-chevron-down ms-2"></i>

            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow">

                <li>

                    <a
                    class="dropdown-item"
                    href="profile.php">

                        <i class="bi bi-person-circle"></i>

                        My Profile

                    </a>

                </li>

                <li>

                    <a
                    class="dropdown-item"
                    href="settings.php">

                        <i class="bi bi-gear"></i>

                        Settings

                    </a>

                </li>

                <li>

                    <hr class="dropdown-divider">

                </li>

                <li>

                    <a
                    class="dropdown-item text-danger"
                    href="../logout.php">

                        <i class="bi bi-box-arrow-right"></i>

                        Logout

                    </a>

                </li>

            </ul>

        </div>

    </div>

</div>