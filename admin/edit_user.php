<?php
require_once "../includes/admin_auth.php";
require_once "../config/config.php";
include "includes/header.php";
include "includes/sidebar.php";

$message = "";
$messageType = "";

/*==================================================
VALIDATE USER ID
==================================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = (int) $_GET['id'];

/*==================================================
GET USER DETAILS
==================================================*/

$stmt = $conn->prepare("
SELECT *
FROM users
WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: users.php");
    exit();
}

/*==================================================
UPDATE USER
==================================================*/

if (isset($_POST['update_user'])) {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = trim($_POST['role']);

    $status = trim($_POST['status']);
    $email_verified = isset($_POST['email_verified']) ? 1 : 0;

    $password = trim($_POST['password']);

    /*==============================================
    VALIDATION
    ==============================================*/

    if (
        empty($full_name) ||
        empty($email) ||
        empty($phone)
    ) {

        $message = "Please fill in all required fields.";
        $messageType = "danger";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Invalid email address.";
        $messageType = "danger";

    } else {

        /*==============================================
        DUPLICATE EMAIL CHECK
        ==============================================*/

        $check = $conn->prepare("
        SELECT user_id
        FROM users
        WHERE email=?
        AND user_id!=?
        ");

        $check->bind_param("si", $email, $user_id);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {

            $message = "Email address already exists.";
            $messageType = "danger";

        } else {

            /*==============================================
            PROFILE PICTURE UPLOAD
            ==============================================*/

            $profile_picture = $user['profile_picture'];

            if (
                isset($_FILES['profile_picture']) &&
                $_FILES['profile_picture']['error'] == 0
            ) {

                $allowed = [
                    "jpg",
                    "jpeg",
                    "png",
                    "webp"
                ];

                $fileName = $_FILES['profile_picture']['name'];
                $fileTmp = $_FILES['profile_picture']['tmp_name'];
                $fileSize = $_FILES['profile_picture']['size'];

                $extension = strtolower(
                    pathinfo($fileName, PATHINFO_EXTENSION)
                );

                if (!in_array($extension, $allowed)) {

                    $message = "Only JPG, JPEG, PNG and WEBP images are allowed.";
                    $messageType = "danger";

                } elseif ($fileSize > 2097152) {

                    $message = "Image size must not exceed 2 MB.";
                    $messageType = "danger";

                } else {

                    $newImage = time() . "_" . rand(1000,9999) . "." . $extension;

                    $uploadPath = "../uploads/profiles/" . $newImage;

                    if (move_uploaded_file($fileTmp, $uploadPath)) {

                        if (
                            !empty($user['profile_picture']) &&
                            file_exists("../uploads/profiles/" . $user['profile_picture'])
                        ) {

                            unlink("../uploads/profiles/" . $user['profile_picture']);

                        }

                        $profile_picture = $newImage;

                    }

                }

            }

            /*==============================================
            UPDATE USER
            ==============================================*/

            if (empty($message)) {

                if (empty($password)) {

                    $stmt = $conn->prepare("
                    UPDATE users
                    SET

                        full_name=?,

                        email=?,

                        phone=?,

                        role=?,

                        status=?,

                        email_verified=?,

                        profile_picture=?

                    WHERE user_id=?
                    ");

                    $stmt->bind_param(
                        "sssssisi",
                        $full_name,
                        $email,
                        $phone,
                        $role,
                        $status,
                        $email_verified,
                        $profile_picture,
                        $user_id
                    );

                } else {

                    $hashedPassword = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $stmt = $conn->prepare("
                    UPDATE users
                    SET

                        full_name=?,

                        email=?,

                        phone=?,

                        role=?,

                        status=?,

                        email_verified=?,

                        password=?,

                        profile_picture=?

                    WHERE user_id=?
                    ");

                    $stmt->bind_param(
                        "sssssissi",
                        $full_name,
                        $email,
                        $phone,
                        $role,
                        $status,
                        $email_verified,
                        $hashedPassword,
                        $profile_picture,
                        $user_id
                    );

                }

                if ($stmt->execute()) {

                    $message = "User updated successfully.";
                    $messageType = "success";

                    /*==============================================
                    REFRESH USER DATA
                    ==============================================*/

                    $stmt = $conn->prepare("
                    SELECT *
                    FROM users
                    WHERE user_id=?
                    ");

                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();

                    $user = $stmt->get_result()->fetch_assoc();

                } else {

                    $message = "Failed to update user.";
                    $messageType = "danger";

                }

            }

        }

    }

}
?>

<div class="container-fluid">

<!-- ==========================================
PAGE HEADER
========================================== -->

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold mb-1">
            <i class="bi bi-pencil-square text-warning"></i>
            Edit User
        </h2>

        <p class="text-muted mb-0">
            Update user account information and permissions.
        </p>

    </div>

    <a href="users.php" class="btn btn-outline-secondary">

        <i class="bi bi-arrow-left"></i>

        Back to Users

    </a>

</div>

<!-- ==========================================
ALERT MESSAGE
========================================== -->

<?php if(!empty($message)){ ?>

<div class="alert alert-<?= $messageType ?> alert-dismissible fade show">

    <?= htmlspecialchars($message) ?>

    <button class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php } ?>

<?php

/*==========================================
ROLE BADGE COLOR
==========================================*/

$roleColor = "secondary";

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

/*==========================================
STATUS COLOR
==========================================*/

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

/*==========================================
PROFILE IMAGE
==========================================*/

$image="../assets/images/default.png";

if(!empty($user['profile_picture'])){

    $image="../uploads/profiles/".$user['profile_picture'];

}

?>

<div class="row">

<!-- ==========================================
LEFT COLUMN
========================================== -->

<div class="col-lg-4">

<div class="card shadow border-0 mb-4">

<div class="card-body text-center">

<img
src="<?= $image ?>"
class="rounded-circle border border-3 mb-3"
width="160"
height="160"
id="profilePreview">

<h3 class="fw-bold">

<?= htmlspecialchars($user['full_name']) ?>

</h3>

<p class="text-muted">

<?= htmlspecialchars($user['email']) ?>

</p>

<div class="mb-3">

<span class="badge bg-<?= $roleColor ?> fs-6">

<?= ucfirst($user['role']) ?>

</span>

<?php if(isset($user['status'])){ ?>

<span class="badge bg-<?= $statusColor ?> fs-6">

<?= ucfirst($user['status']) ?>

</span>

<?php } ?>

</div>

<div class="small text-muted">

Member since

<strong>

<?= date("d M Y",strtotime($user['created_at'])) ?>

</strong>

</div>

</div>

</div>

<!-- ACCOUNT INFORMATION -->

<div class="card shadow border-0 mb-4">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="bi bi-person-vcard"></i>

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

<th>Joined</th>

<td><?= date("d M Y",strtotime($user['created_at'])) ?></td>

</tr>

<?php if(isset($user['last_login'])){ ?>

<tr>

<th>Last Login</th>

<td><?= $user['last_login'] ?></td>

</tr>

<?php } ?>

<?php if(isset($user['email_verified'])){ ?>

<tr>

<th>Email</th>

<td>

<?php if($user['email_verified']){ ?>

<span class="badge bg-success">

Verified

</span>

<?php }else{ ?>

<span class="badge bg-danger">

Not Verified

</span>

<?php } ?>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

<!-- QUICK ACTIONS -->

<div class="card shadow border-0">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="bi bi-lightning-charge"></i>

Quick Actions

</h5>

</div>

<div class="card-body d-grid gap-2">

<a
href="view_user.php?id=<?= $user['user_id'] ?>"
class="btn btn-outline-primary">

<i class="bi bi-eye"></i>

View Profile

</a>

<a
href="users.php"
class="btn btn-outline-secondary">

<i class="bi bi-people"></i>

All Users

</a>

<button
type="reset"
class="btn btn-outline-warning">

<i class="bi bi-arrow-clockwise"></i>

Reset Form

</button>

</div>

</div>

</div>

<!-- ==========================================
RIGHT COLUMN
========================================== -->

<div class="col-lg-8">

<!-- USER STATISTICS -->

<div class="row mb-4">

<div class="col-md-4">

<div class="card border-0 shadow text-center">

<div class="card-body">

<i class="bi bi-person-check fs-1 text-success"></i>

<h3 class="mt-2">

<?= $user['user_id'] ?>

</h3>

<p class="text-muted mb-0">

User ID

</p>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card border-0 shadow text-center">

<div class="card-body">

<i class="bi bi-calendar-event fs-1 text-primary"></i>

<h4>

<?= date("Y",strtotime($user['created_at'])) ?>

</h4>

<p class="text-muted mb-0">

Joined Year

</p>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card border-0 shadow text-center">

<div class="card-body">

<i class="bi bi-shield-check fs-1 text-warning"></i>

<h4>

<?= ucfirst($user['role']) ?>

</h4>

<p class="text-muted mb-0">

Current Role

</p>

</div>

</div>

</div>

</div>

<!-- ==========================================
EDIT FORM CARD STARTS HERE
========================================== -->

<div class="card shadow border-0">

<div class="card-header bg-warning">

<h5 class="mb-0">

<i class="bi bi-pencil-square"></i>

Edit User Details

</h5>

</div>

<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<!-- ==========================================
PERSONAL INFORMATION
========================================== -->

<h5 class="mb-3 text-primary">

<i class="bi bi-person-lines-fill"></i>

Personal Information

</h5>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Full Name <span class="text-danger">*</span>

</label>

<input
type="text"
name="full_name"
class="form-control"
required
value="<?= htmlspecialchars($user['full_name']) ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Email Address <span class="text-danger">*</span>

</label>

<input
type="email"
name="email"
class="form-control"
required
value="<?= htmlspecialchars($user['email']) ?>">

</div>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Phone Number <span class="text-danger">*</span>

</label>

<input
type="text"
name="phone"
class="form-control"
required
value="<?= htmlspecialchars($user['phone']) ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Profile Picture

</label>

<input
type="file"
name="profile_picture"
id="profile_picture"
class="form-control"
accept=".jpg,.jpeg,.png,.webp">

<div class="form-text">

Accepted formats:
JPG, JPEG, PNG and WEBP (Maximum 2 MB)

</div>

</div>

</div>

<hr>

<!-- ==========================================
ACCOUNT SETTINGS
========================================== -->

<h5 class="mb-3 text-success">

<i class="bi bi-gear-fill"></i>

Account Settings

</h5>

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">

User Role

</label>

<select
name="role"
class="form-select">

<option value="jobseeker"
<?= $user['role']=="jobseeker"?"selected":"" ?>>

Job Seeker

</option>

<option value="employer"
<?= $user['role']=="employer"?"selected":"" ?>>

Employer

</option>

<option value="admin"
<?= $user['role']=="admin"?"selected":"" ?>>

Administrator

</option>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Account Status

</label>

<select
name="status"
class="form-select">

<option value="active"
<?= (isset($user['status']) && $user['status']=="active")?"selected":"" ?>>

Active

</option>

<option value="pending"
<?= (isset($user['status']) && $user['status']=="pending")?"selected":"" ?>>

Pending

</option>

<option value="suspended"
<?= (isset($user['status']) && $user['status']=="suspended")?"selected":"" ?>>

Suspended

</option>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Email Verification

</label>

<div class="form-check mt-2">

<input
class="form-check-input"
type="checkbox"
name="email_verified"
id="email_verified"
value="1"
<?= (!empty($user['email_verified'])) ? "checked" : "" ?>>

<label
class="form-check-label"
for="email_verified">

Email Verified

</label>

</div>

</div>

</div>

<hr>

<!-- ==========================================
CHANGE PASSWORD
========================================== -->

<h5 class="mb-3 text-danger">

<i class="bi bi-lock-fill"></i>

Change Password

</h5>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

New Password

</label>

<input
type="password"
name="password"
id="password"
class="form-control">

<div class="form-text">

Leave blank to keep the current password.

</div>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Confirm Password

</label>

<input
type="password"
id="confirm_password"
class="form-control">

<div
id="passwordMatch"
class="small mt-2">

</div>

</div>

</div>

<div class="mb-3">

<label class="form-label">

Password Strength

</label>

<div class="progress">

<div
class="progress-bar"
id="passwordStrength"
role="progressbar"
style="width:0%">

Weak

</div>

</div>

</div>

<hr>

<!-- ==========================================
FORM BUTTONS
========================================== -->

<div class="d-flex justify-content-between">

<a
href="users.php"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Cancel

</a>

<div>

<button
type="reset"
class="btn btn-warning me-2">

<i class="bi bi-arrow-clockwise"></i>

Reset

</button>

<button
type="submit"
name="update_user"
class="btn btn-success">

<i class="bi bi-check-circle-fill"></i>

Save Changes

</button>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<!-- ==========================================
UPDATE CONFIRMATION MODAL
========================================== -->

<div class="modal fade"
     id="confirmUpdateModal"
     tabindex="-1"
     aria-labelledby="confirmUpdateModalLabel"
     aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-success text-white">

                <h5 class="modal-title" id="confirmUpdateModalLabel">

                    <i class="bi bi-check-circle"></i>

                    Confirm Update

                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                Are you sure you want to save these changes?

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Cancel

                </button>

                <button
                    type="button"
                    id="confirmSubmit"
                    class="btn btn-success">

                    <i class="bi bi-check-circle-fill"></i>

                    Save Changes

                </button>

            </div>

        </div>

    </div>

</div>

<script>

/*=========================================
PROFILE IMAGE PREVIEW
=========================================*/

const imageInput = document.getElementById('profile_picture');

if(imageInput){

    imageInput.addEventListener('change', function(e){

        const file = e.target.files[0];

        if(file){

            document.getElementById('profilePreview').src =
                URL.createObjectURL(file);

        }

    });

}

/*=========================================
PASSWORD STRENGTH
=========================================*/

const password = document.getElementById("password");
const strength = document.getElementById("passwordStrength");

if(password){

password.addEventListener("keyup", function(){

let score = 0;

const value = password.value;

if(value.length >= 8) score++;

if(/[A-Z]/.test(value)) score++;

if(/[a-z]/.test(value)) score++;

if(/[0-9]/.test(value)) score++;

if(/[^A-Za-z0-9]/.test(value)) score++;

const percent = score * 20;

strength.style.width = percent + "%";

strength.className = "progress-bar";

if(score <= 2){

strength.classList.add("bg-danger");

strength.innerHTML = "Weak";

}
else if(score == 3){

strength.classList.add("bg-warning");

strength.innerHTML = "Fair";

}
else if(score == 4){

strength.classList.add("bg-info");

strength.innerHTML = "Good";

}
else{

strength.classList.add("bg-success");

strength.innerHTML = "Strong";

}

});

}

/*=========================================
CONFIRM PASSWORD
=========================================*/

const confirmPassword = document.getElementById("confirm_password");
const passwordMatch = document.getElementById("passwordMatch");

if(confirmPassword){

confirmPassword.addEventListener("keyup", function(){

if(password.value === "" && confirmPassword.value===""){

passwordMatch.innerHTML="";

return;

}

if(password.value === confirmPassword.value){

passwordMatch.innerHTML =
"<span class='text-success'><i class='bi bi-check-circle'></i> Passwords match</span>";

}
else{

passwordMatch.innerHTML =
"<span class='text-danger'><i class='bi bi-x-circle'></i> Passwords do not match</span>";

}

});

}

/*=========================================
UNSAVED CHANGES WARNING
=========================================*/

let formChanged = false;

const form = document.querySelector("form");

form.querySelectorAll("input, select, textarea").forEach(function(element){

element.addEventListener("change", function(){

formChanged = true;

});

});

window.addEventListener("beforeunload", function(e){

if(formChanged){

e.preventDefault();

e.returnValue = "";

}

});

/*=========================================
CONFIRM BEFORE SAVE
=========================================*/

form.addEventListener("submit", function(e){

e.preventDefault();

if(password.value !== confirmPassword.value){

alert("Passwords do not match.");

return;

}

const modal = new bootstrap.Modal(

document.getElementById("confirmUpdateModal")

);

modal.show();

});

document.getElementById("confirmSubmit").addEventListener("click", function(){

formChanged = false;

form.submit();

});

</script>

<?php include "includes/footer.php"; ?>