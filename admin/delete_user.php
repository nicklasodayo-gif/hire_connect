<?php
session_start();

require_once "../includes/admin_auth.php";
require_once "../config/config.php";
include "includes/header.php";
include "includes/sidebar.php";

$message = "";
$messageType = "danger";

/*==================================================
VALIDATE USER ID
==================================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location: users.php");
    exit();

}

$user_id = (int)$_GET['id'];

/*==================================================
PREVENT SELF DELETION
==================================================*/

if (
    isset($_SESSION['user_id']) &&
    $user_id == $_SESSION['user_id']
) {

    $_SESSION['error'] =
        "You cannot delete your own administrator account.";

    header("Location: users.php");
    exit();

}

/*==================================================
GET USER
==================================================*/

$stmt = $conn->prepare("
SELECT *
FROM users
WHERE user_id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

if(!$user){

    $_SESSION['error']="User not found.";

    header("Location: users.php");

    exit();

}

/*==================================================
DELETE USER
==================================================*/

if(isset($_POST['delete_user'])){

$conn->begin_transaction();

try{

/*==================================================
DELETE PROFILE PICTURE
==================================================*/

if(
!empty($user['profile_picture']) &&
file_exists("../uploads/profiles/".$user['profile_picture'])
){

unlink("../uploads/profiles/".$user['profile_picture']);

}

/*==================================================
DELETE RESUME
==================================================*/

if(
!empty($user['resume']) &&
file_exists("../uploads/resumes/".$user['resume'])
){

unlink("../uploads/resumes/".$user['resume']);

}

/*==================================================
DELETE SAVED JOBS
==================================================*/

$stmt=$conn->prepare("
DELETE
FROM saved_jobs
WHERE applicant_id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

/*==================================================
DELETE NOTIFICATIONS
==================================================*/

$stmt=$conn->prepare("
DELETE
FROM notifications
WHERE user_id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

/*==================================================
DELETE INTERVIEWS
FOR JOB SEEKER
==================================================*/

$stmt=$conn->prepare("
DELETE i
FROM interviews i

INNER JOIN applications a

ON a.application_id=i.application_id

WHERE a.applicant_id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

/*==================================================
DELETE APPLICATION HISTORY
==================================================*/

$stmt=$conn->prepare("
DELETE
FROM application_history

WHERE application_id IN(

SELECT application_id

FROM applications

WHERE applicant_id=?

)
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

/*==================================================
DELETE APPLICATIONS
==================================================*/

$stmt=$conn->prepare("
DELETE
FROM applications

WHERE applicant_id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

/*==================================================
EMPLOYER DATA
==================================================*/

if($user['role']=="employer"){

/*-----------------------------------------
Delete interviews
-----------------------------------------*/

$stmt=$conn->prepare("
DELETE i

FROM interviews i

INNER JOIN applications a

ON a.application_id=i.application_id

INNER JOIN jobs j

ON j.job_id=a.job_id

WHERE j.employer_id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

/*-----------------------------------------
Delete application history
-----------------------------------------*/

$stmt=$conn->prepare("
DELETE ah

FROM application_history ah

INNER JOIN applications a

ON ah.application_id=a.application_id

INNER JOIN jobs j

ON j.job_id=a.job_id

WHERE j.employer_id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

/*-----------------------------------------
Delete applications
-----------------------------------------*/

$stmt=$conn->prepare("
DELETE a

FROM applications a

INNER JOIN jobs j

ON a.job_id=j.job_id

WHERE j.employer_id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

/*-----------------------------------------
Delete company logo
(optional)
-----------------------------------------*/

$stmt=$conn->prepare("
SELECT company_logo

FROM jobs

WHERE employer_id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$logos=$stmt->get_result();

while($logo=$logos->fetch_assoc()){

if(
!empty($logo['company_logo']) &&
file_exists("../uploads/logos/".$logo['company_logo'])
){

unlink("../uploads/logos/".$logo['company_logo']);

}

}

/*-----------------------------------------
Delete jobs
-----------------------------------------*/

$stmt=$conn->prepare("
DELETE

FROM jobs

WHERE employer_id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

}

/*==================================================
DELETE USER
==================================================*/

$stmt=$conn->prepare("
DELETE

FROM users

WHERE user_id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

/*==================================================
COMMIT
==================================================*/

$conn->commit();

$_SESSION['success']="User deleted successfully.";

header("Location: users.php");

exit();

}catch(Exception $e){

$conn->rollback();

$message=$e->getMessage();

$messageType="danger";

}

}
?>

<div class="container-fluid">

<!-- ==========================================
PAGE HEADER
========================================== -->

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold text-danger">

            <i class="bi bi-trash-fill"></i>

            Delete User

        </h2>

        <p class="text-muted mb-0">

            Permanently remove a user account and all associated records.

        </p>

    </div>

    <a href="users.php" class="btn btn-outline-secondary">

        <i class="bi bi-arrow-left"></i>

        Back to Users

    </a>

</div>

<?php if(!empty($message)){ ?>

<div class="alert alert-<?= $messageType ?> alert-dismissible fade show">

    <?= htmlspecialchars($message) ?>

    <button
        class="btn-close"
        data-bs-dismiss="alert">
    </button>

</div>

<?php } ?>

<?php

/*=========================================
ROLE COLOR
=========================================*/

$roleColor="secondary";

switch($user['role']){

    case "admin":
        $roleColor="danger";
        break;

    case "employer":
        $roleColor="primary";
        break;

    case "jobseeker":
        $roleColor="success";
        break;

}

/*=========================================
STATUS COLOR
=========================================*/

$statusColor="secondary";

if(isset($user['status'])){

    switch(strtolower($user['status'])){

        case "active":
            $statusColor="success";
            break;

        case "pending":
            $statusColor="warning";
            break;

        case "suspended":
            $statusColor="danger";
            break;

    }

}

/*=========================================
PROFILE IMAGE
=========================================*/

$image="../assets/images/default.png";

if(!empty($user['profile_picture'])){

    $image="../uploads/profiles/".$user['profile_picture'];

}

?>

<div class="row">

<!-- ==========================================
LEFT SIDEBAR
========================================== -->

<div class="col-lg-4">

<div class="card shadow border-danger mb-4">

<div class="card-body text-center">

<img
src="<?= $image ?>"
class="rounded-circle border border-3 border-danger mb-3"
width="160"
height="160">

<h4>

<?= htmlspecialchars($user['full_name']) ?>

</h4>

<p class="text-muted">

<?= htmlspecialchars($user['email']) ?>

</p>

<div class="mb-3">

<span class="badge bg-<?= $roleColor ?>">

<?= ucfirst($user['role']) ?>

</span>

<?php if(isset($user['status'])){ ?>

<span class="badge bg-<?= $statusColor ?>">

<?= ucfirst($user['status']) ?>

</span>

<?php } ?>

</div>

</div>

</div>

<!-- ACCOUNT INFORMATION -->

<div class="card shadow mb-4">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="bi bi-person-badge"></i>

Account Information

</h5>

</div>

<div class="card-body">

<table class="table table-borderless mb-0">

<tr>

<th width="45%">User ID</th>

<td>#<?= $user['user_id'] ?></td>

</tr>

<tr>

<th>Role</th>

<td><?= ucfirst($user['role']) ?></td>

</tr>

<tr>

<th>Email</th>

<td><?= htmlspecialchars($user['email']) ?></td>

</tr>

<tr>

<th>Joined</th>

<td><?= date("d M Y",strtotime($user['created_at'])) ?></td>

</tr>

<?php if(isset($user['last_login'])){ ?>

<tr>

<th>Last Login</th>

<td><?= $user['last_login'] ?></td>

</tr>

<?php } ?>

</table>

</div>

</div>

<!-- DANGER NOTICE -->

<div class="card shadow border-danger">

<div class="card-header bg-danger text-white">

<h5 class="mb-0">

<i class="bi bi-exclamation-triangle-fill"></i>

Warning

</h5>

</div>

<div class="card-body">

<p class="mb-0">

This action is permanent and cannot be undone.

All associated records belonging to this user will also be permanently removed.

</p>

</div>

</div>

</div>

<!-- ==========================================
RIGHT COLUMN
========================================== -->

<div class="col-lg-8">

<!-- SUMMARY CARDS -->

<div class="row mb-4">

<div class="col-md-4">

<div class="card shadow text-center border-danger">

<div class="card-body">

<i class="bi bi-person-fill fs-1 text-danger"></i>

<h3>

<?= $user['user_id'] ?>

</h3>

<p class="text-muted mb-0">

User ID

</p>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card shadow text-center">

<div class="card-body">

<i class="bi bi-calendar-event fs-1 text-primary"></i>

<h3>

<?= date("Y",strtotime($user['created_at'])) ?>

</h3>

<p class="text-muted mb-0">

Member Since

</p>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card shadow text-center">

<div class="card-body">

<i class="bi bi-shield-lock fs-1 text-warning"></i>

<h4>

<?= ucfirst($user['role']) ?>

</h4>

<p class="text-muted mb-0">

Account Type

</p>

</div>

</div>

</div>

</div>

<!-- DELETE IMPACT -->

<div class="card shadow border-danger mb-4">

<div class="card-header bg-danger text-white">

<h5 class="mb-0">

<i class="bi bi-trash3-fill"></i>

Items That Will Be Deleted

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

<i class="bi bi-person"></i>

User Account

</li>

<li class="list-group-item">

<i class="bi bi-image"></i>

Profile Picture

</li>

<li class="list-group-item">

<i class="bi bi-file-earmark-pdf"></i>

Resume

</li>

<li class="list-group-item">

<i class="bi bi-bookmark-star"></i>

Saved Jobs

</li>

</ul>

</div>

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

<i class="bi bi-briefcase"></i>

Applications

</li>

<li class="list-group-item">

<i class="bi bi-clock-history"></i>

Application History

</li>

<li class="list-group-item">

<i class="bi bi-calendar2-event"></i>

Interview Records

</li>

<li class="list-group-item">

<i class="bi bi-bell"></i>

Notifications

</li>

</ul>

</div>

</div>

<?php if($user['role']=="employer"){ ?>

<hr>

<div class="alert alert-warning mb-0">

<strong>Employer Account:</strong>

All jobs, company logos, applications, interview schedules, and related records created by this employer will also be deleted.

</div>

<?php } ?>

</div>

</div>

<!-- DELETE FORM STARTS HERE -->

<form method="POST">

<!-- ==========================================
FINAL CONFIRMATION
========================================== -->

<div class="card shadow border-danger mb-4">

<div class="card-header bg-warning">

<h5 class="mb-0">

<i class="bi bi-exclamation-octagon-fill"></i>

Final Confirmation

</h5>

</div>

<div class="card-body">

<div class="alert alert-danger">

<h5 class="mb-2">

This action cannot be undone.

</h5>

<p class="mb-0">

To prevent accidental deletion, please complete the confirmation below.

</p>

</div>

<!-- TYPE DELETE -->

<div class="mb-4">

<label class="form-label fw-bold">

Type

<span class="text-danger">

DELETE

</span>

to confirm

</label>

<input
type="text"
id="deleteText"
class="form-control"
placeholder="Type DELETE">

<div class="form-text">

Deletion is only enabled when the word
<strong>DELETE</strong>
is entered exactly.

</div>

</div>

<!-- CONFIRM CHECKBOX -->

<div class="form-check mb-4">

<input
class="form-check-input"
type="checkbox"
id="confirmDelete">

<label
class="form-check-label"
for="confirmDelete">

I understand that this action is permanent and all related records will be permanently removed.

</label>

</div>

<hr>

<!-- ACTION BUTTONS -->

<div class="d-flex justify-content-between">

<a
href="users.php"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Cancel

</a>

<button
type="button"
id="deleteButton"
class="btn btn-danger"
disabled>

<i class="bi bi-trash-fill"></i>

Delete Permanently

</button>

</div>

</div>

</div>

<!-- LOADING MESSAGE -->

<div
id="deleteLoading"
class="text-center mt-4"
style="display:none;">

<div
class="spinner-border text-danger"
role="status">

<span class="visually-hidden">

Loading...

</span>

</div>

<p class="mt-3 fw-bold text-danger">

Deleting user...

Please wait.

</p>

</div>

</form>

<!-- ==========================================
CONFIRM DELETE MODAL
========================================== -->

<div
class="modal fade"
id="deleteModal"
tabindex="-1">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content border-danger">

<div class="modal-header bg-danger text-white">

<h5 class="modal-title">

<i class="bi bi-trash-fill"></i>

Confirm Permanent Deletion

</h5>

<button
type="button"
class="btn-close btn-close-white"
data-bs-dismiss="modal">

</button>

</div>

<div class="modal-body">

<div class="alert alert-warning">

<strong>

Warning!

</strong>

You are about to permanently delete:

<ul class="mt-2 mb-0">

<li>User account</li>

<li>Applications</li>

<li>Interview records</li>

<li>Saved jobs</li>

<li>Notifications</li>

<li>Uploaded files</li>

<?php if($user['role']=="employer"){ ?>

<li>Employer jobs</li>

<li>Company logos</li>

<li>Employer applications</li>

<?php } ?>

</ul>

</div>

<p class="mb-0">

This action
<strong>cannot be reversed.</strong>

</p>

</div>

<div class="modal-footer">

<button
type="button"
class="btn btn-secondary"
data-bs-dismiss="modal">

Cancel

</button>

<button
type="submit"
name="delete_user"
id="confirmDeleteBtn"
class="btn btn-danger">

<i class="bi bi-trash-fill"></i>

Yes, Delete Permanently

</button>

</div>

</div>

</div>

</div>

<!-- ==========================================
JAVASCRIPT
========================================== -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const deleteInput = document.getElementById("deleteText");
    const confirmCheck = document.getElementById("confirmDelete");
    const deleteButton = document.getElementById("deleteButton");
    const deleteForm = document.querySelector("form");
    const loading = document.getElementById("deleteLoading");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

    /*=========================================
    ENABLE DELETE BUTTON
    =========================================*/

    function validateDelete() {

        const validText =
            deleteInput.value.trim() === "DELETE";

        const checked =
            confirmCheck.checked;

        deleteButton.disabled = !(validText && checked);

    }

    deleteInput.addEventListener("keyup", validateDelete);
    confirmCheck.addEventListener("change", validateDelete);

    /*=========================================
    SHOW CONFIRMATION MODAL
    =========================================*/

    deleteButton.addEventListener("click", function () {

        const modal = new bootstrap.Modal(
            document.getElementById("deleteModal")
        );

        modal.show();

    });

    /*=========================================
    FINAL DELETE
    =========================================*/

    confirmDeleteBtn.addEventListener("click", function () {

        confirmDeleteBtn.disabled = true;

        confirmDeleteBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';

        loading.style.display = "block";

        deleteForm.submit();

    });

});


/*=========================================
PROFILE IMAGE FALLBACK
=========================================*/

const profileImage = document.querySelector("img.rounded-circle");

if(profileImage){

    profileImage.onerror = function(){

        this.src = "../assets/images/default.png";

    };

}

</script>

<?php include "includes/footer.php"; ?>