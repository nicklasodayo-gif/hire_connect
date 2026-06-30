<?php
require_once "../includes/admin_auth.php";
require_once "../config/config.php";
include "includes/header.php";
include "includes/sidebar.php";

/*==========================================
VALIDATE USER ID
==========================================*/
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = (int)$_GET['id'];

/*==========================================
GET USER DETAILS
==========================================*/

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

if(!$user){
    echo "<div class='alert alert-danger'>User not found.</div>";
    include "includes/footer.php";
    exit();
}

/*==========================================
STATISTICS
==========================================*/

$totalJobs = 0;
$totalApplications = 0;
$totalInterviews = 0;

if($user['role']=="employer"){

    $stmt=$conn->prepare("SELECT COUNT(*) total FROM jobs WHERE employer_id=?");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $totalJobs=$stmt->get_result()->fetch_assoc()['total'];

}

if($user['role']=="jobseeker"){

    $stmt=$conn->prepare("SELECT COUNT(*) total FROM applications WHERE applicant_id=?");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $totalApplications=$stmt->get_result()->fetch_assoc()['total'];

    $stmt=$conn->prepare("
    SELECT COUNT(*) total
    FROM interviews i
    JOIN applications a
    ON i.application_id=a.application_id
    WHERE a.applicant_id=?
    ");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $totalInterviews=$stmt->get_result()->fetch_assoc()['total'];

}

/*==========================================
EMPLOYER JOBS
==========================================*/

$jobs=[];

if($user['role']=="employer"){

$stmt=$conn->prepare("
SELECT *
FROM jobs
WHERE employer_id=?
ORDER BY created_at DESC
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$jobs=$stmt->get_result();

}

/*==========================================
APPLICATIONS
==========================================*/

$applications=[];

if($user['role']=="jobseeker"){

$stmt=$conn->prepare("
SELECT
j.title,
a.status,
a.applied_at
FROM applications a
JOIN jobs j
ON j.job_id=a.job_id
WHERE applicant_id=?
ORDER BY applied_at DESC
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$applications=$stmt->get_result();

}

/*==========================================
INTERVIEWS
==========================================*/

$interviews=[];

if($user['role']=="jobseeker"){

$stmt=$conn->prepare("
SELECT
j.title,
i.interview_date,
i.interview_time,
i.status
FROM interviews i
JOIN applications a
ON a.application_id=i.application_id
JOIN jobs j
ON j.job_id=a.job_id
WHERE a.applicant_id=?
ORDER BY interview_date DESC
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$interviews=$stmt->get_result();

}

/*==========================================
ROLE COLORS
==========================================*/

$roleColor="secondary";

if($user['role']=="admin") $roleColor="danger";
if($user['role']=="employer") $roleColor="primary";
if($user['role']=="jobseeker") $roleColor="success";
?>

<div class="container-fluid">

<div class="d-flex justify-content-between mb-4">

<h2>
<i class="bi bi-person-circle"></i>
User Profile
</h2>

<a href="users.php" class="btn btn-secondary">
<i class="bi bi-arrow-left"></i>
Back
</a>

</div>

<!-- PROFILE CARD -->

<div class="card shadow mb-4">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-2 text-center">

<img
src="../uploads/profiles/<?=
!empty($user['profile_picture'])
? $user['profile_picture']
: 'default.png';
?>"
class="rounded-circle border"
width="130"
height="130">

</div>

<div class="col-md-7">

<h3><?= htmlspecialchars($user['full_name']); ?></h3>

<p class="text-muted mb-1">

<?= htmlspecialchars($user['email']); ?>

</p>

<span class="badge bg-<?= $roleColor ?>">

<?= ucfirst($user['role']); ?>

</span>

<p class="mt-2">

Joined

<strong>

<?= date("d M Y",strtotime($user['created_at'])); ?>

</strong>

</p>

</div>

<div class="col-md-3 text-end">

<a href="edit_user.php?id=<?= $user_id ?>" class="btn btn-primary mb-2 w-100">

<i class="bi bi-pencil-square"></i>

Edit User

</a>

<?php if($user['role']=="jobseeker" && !empty($user['resume'])){ ?>

<a
href="../uploads/resumes/<?= $user['resume']; ?>"
target="_blank"
class="btn btn-success w-100">

<i class="bi bi-file-earmark-pdf"></i>

View Resume

</a>

<?php } ?>

</div>

</div>

</div>

</div>

<!-- INFORMATION -->

<div class="row mb-4">

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-primary text-white">

Personal Information

</div>

<div class="card-body">

<p><strong>User ID:</strong> <?= $user['user_id']; ?></p>

<p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']); ?></p>

<p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>

</div>

</div>

</div>

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-dark text-white">

Account Information

</div>

<div class="card-body">

<p><strong>Role:</strong> <?= ucfirst($user['role']); ?></p>

<p><strong>Joined:</strong> <?= date("d M Y",strtotime($user['created_at'])); ?></p>

</div>

</div>

</div>

</div>

<!-- STATISTICS -->

<div class="row mb-4">

<?php if($user['role']=="employer"){ ?>

<div class="col-md-4">

<div class="card text-center border-primary shadow">

<div class="card-body">

<h2><?= $totalJobs ?></h2>

Jobs Posted

</div>

</div>

</div>

<?php } ?>

<?php if($user['role']=="jobseeker"){ ?>

<div class="col-md-4">

<div class="card shadow text-center">

<div class="card-body">

<h2><?= $totalApplications ?></h2>

Applications

</div>

</div>

</div>

<div class="col-md-4">

<div class="card shadow text-center">

<div class="card-body">

<h2><?= $totalInterviews ?></h2>

Interviews

</div>

</div>

</div>

<?php } ?>

</div>

<!-- EMPLOYER JOBS -->

<?php if($user['role']=="employer"){ ?>

<div class="card shadow mb-4">

<div class="card-header bg-success text-white">

Jobs Posted

</div>

<table class="table table-hover">

<thead>

<tr>

<th>Title</th>

<th>Category</th>

<th>Status</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php if($jobs->num_rows>0){ ?>

<?php while($job=$jobs->fetch_assoc()){ ?>

<tr>

<td><?= htmlspecialchars($job['title']); ?></td>

<td><?= htmlspecialchars($job['category']); ?></td>

<td>

<?php

$badge="secondary";

if($job['status']=="active") $badge="success";
if($job['status']=="closed") $badge="danger";
if($job['status']=="draft") $badge="warning";

?>

<span class="badge bg-<?= $badge ?>">

<?= ucfirst($job['status']) ?>

</span>

</td>

<td><?= date("d M Y",strtotime($job['created_at'])); ?></td>

</tr>

<?php } ?>

<?php }else{ ?>

<tr>

<td colspan="4" class="text-center text-muted">

No jobs posted.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<?php } ?>

<!-- APPLICATIONS -->

<?php if($user['role']=="jobseeker"){ ?>

<div class="card shadow mb-4">

<div class="card-header bg-info text-white">

Applications

</div>

<table class="table table-striped">

<thead>

<tr>

<th>Job</th>

<th>Status</th>

<th>Applied</th>

</tr>

</thead>

<tbody>

<?php if($applications->num_rows>0){ ?>

<?php while($app=$applications->fetch_assoc()){ ?>

<tr>

<td><?= htmlspecialchars($app['title']); ?></td>

<td><?= htmlspecialchars($app['status']); ?></td>

<td><?= date("d M Y",strtotime($app['applied_at'])); ?></td>

</tr>

<?php } ?>

<?php }else{ ?>

<tr>

<td colspan="3" class="text-center text-muted">

No applications found.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<?php } ?>

<!-- INTERVIEWS -->

<?php if($user['role']=="jobseeker"){ ?>

<div class="card shadow">

<div class="card-header bg-warning">

Interview History

</div>

<table class="table">

<thead>

<tr>

<th>Job</th>

<th>Date</th>

<th>Time</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php if($interviews->num_rows>0){ ?>

<?php while($i=$interviews->fetch_assoc()){ ?>

<tr>

<td><?= htmlspecialchars($i['title']); ?></td>

<td><?= date("d M Y",strtotime($i['interview_date'])); ?></td>

<td><?= date("g:i A",strtotime($i['interview_time'])); ?></td>

<td><?= htmlspecialchars($i['status']); ?></td>

</tr>

<?php } ?>

<?php }else{ ?>

<tr>

<td colspan="4" class="text-center text-muted">

No interviews scheduled.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<?php } ?>

</div>

<?php include "includes/footer.php"; ?>