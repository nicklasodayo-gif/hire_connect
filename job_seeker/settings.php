<?php
require_once "../includes/jobseeker_auth.php";
require_once "../config/config.php";

$user_id = $_SESSION['user_id'];
$message = "";

/*==============================
UPDATE PASSWORD
==============================*/

if(isset($_POST['change_password'])){

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $query = mysqli_query($conn,"
        SELECT password
        FROM users
        WHERE user_id='$user_id'
    ");

    $user = mysqli_fetch_assoc($query);

    if(password_verify($current_password,$user['password'])){

        if($new_password==$confirm_password){

            $hashed = password_hash($new_password,PASSWORD_DEFAULT);

            mysqli_query($conn,"
                UPDATE users
                SET password='$hashed'
                WHERE user_id='$user_id'
            ");

            $message = "
            <div class='alert alert-success'>
                Password changed successfully.
            </div>";

        }else{

            $message = "
            <div class='alert alert-danger'>
                New passwords do not match.
            </div>";

        }

    }else{

        $message = "
        <div class='alert alert-danger'>
            Current password is incorrect.
        </div>";

    }

}

/*==============================
NOTIFICATION SETTINGS
==============================*/

if(isset($_POST['save_notifications'])){

    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;

    $job_alerts = isset($_POST['job_alerts']) ? 1 : 0;

    mysqli_query($conn,"
    INSERT INTO jobseeker_settings(
        user_id,
        email_notifications,
        job_alerts
    )

    VALUES(
        '$user_id',
        '$email_notifications',
        '$job_alerts'
    )

    ON DUPLICATE KEY UPDATE

    email_notifications='$email_notifications',

    job_alerts='$job_alerts'
    ");

    $message = "
    <div class='alert alert-success'>
        Notification settings updated.
    </div>";

}

/*==============================
LOAD SETTINGS
==============================*/

$settings = mysqli_query($conn,"
SELECT *
FROM jobseeker_settings
WHERE user_id='$user_id'
");

$setting = mysqli_fetch_assoc($settings);

/*==============================
DELETE ACCOUNT
==============================*/

if(isset($_POST['delete_account'])){

    mysqli_query($conn,"
        DELETE FROM users
        WHERE user_id='$user_id'
    ");

    session_destroy();

    header("Location: ../index.php");
    exit();

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Settings</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">

<link
rel="stylesheet"
href="assets/css/jobseeker.css">

</head>

<body>

<?php include("includes/sidebar.php"); ?>

<div class="main-content">

<?php include("includes/topbar.php"); ?>

<div class="container mt-4">

<?= $message; ?>

<div class="row">

<!-- PASSWORD -->

<div class="col-lg-6">

<div class="card shadow mb-4">

<div class="card-header bg-primary text-white">

<h5>

<i class="bi bi-lock-fill"></i>

Change Password

</h5>

</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">

<label>

Current Password

</label>

<input
type="password"
name="current_password"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

New Password

</label>

<input
type="password"
name="new_password"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

Confirm Password

</label>

<input
type="password"
name="confirm_password"
class="form-control"
required>

</div>

<button
class="btn btn-primary"
name="change_password">

<i class="bi bi-key"></i>

Update Password

</button>

</form>

</div>

</div>

</div>

<!-- NOTIFICATIONS -->

<div class="col-lg-6">

<div class="card shadow mb-4">

<div class="card-header bg-success text-white">

<h5>

<i class="bi bi-bell-fill"></i>

Notification Preferences

</h5>

</div>

<div class="card-body">

<form method="POST">

<div class="form-check">

<input
class="form-check-input"
type="checkbox"
name="email_notifications"

<?= (!empty($setting['email_notifications'])) ? "checked" : ""; ?>>

<label class="form-check-label">

Receive Email Notifications

</label>

</div>

<div class="form-check mb-3">

<input
class="form-check-input"
type="checkbox"
name="job_alerts"

<?= (!empty($setting['job_alerts'])) ? "checked" : ""; ?>>

<label class="form-check-label">

Receive Job Alerts

</label>

</div>

<button
class="btn btn-success"
name="save_notifications">

Save Preferences

</button>

</form>

</div>

</div>

</div>

</div>

<!-- ACCOUNT INFORMATION -->

<div class="card shadow">

<div class="card-header bg-dark text-white">

<h5>

<i class="bi bi-person-circle"></i>

Account Information

</h5>

</div>

<div class="card-body">

<table class="table">

<tr>

<th>Name</th>

<?= htmlspecialchars($user['full_name']); ?>

</tr>

<tr>

<th>Email</th>

<td><?= htmlspecialchars($_SESSION['email']); ?></td>

</tr>

<tr>

<th>Role</th>

<td><?= ucfirst(htmlspecialchars($_SESSION['role'])); ?></td>

</tr>

</table>

</div>

</div>

<!-- DELETE ACCOUNT -->

<div class="card shadow mt-4 border-danger">

<div class="card-header bg-danger text-white">

<h5>

<i class="bi bi-trash-fill"></i>

Danger Zone

</h5>

</div>

<div class="card-body">

<p>

Deleting your account is permanent.

All applications, saved jobs and profile information will be removed.

</p>

<form
method="POST"
onsubmit="return confirm('Are you sure you want to permanently delete your account?');">

<button
class="btn btn-danger"
name="delete_account">

Delete My Account

</button>

</form>

</div>

</div>

</div>

<?php include("includes/footer.php"); ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>