<?php
require_once "../includes/jobseeker_auth.php";
require_once "../config/config.php";

// Statistics
$user_id = $_SESSION['user_id'];

// Applied Jobs
$applied = mysqli_query($conn,
"SELECT COUNT(*) AS total FROM applications WHERE applicant_id='$user_id'");
$applied = mysqli_fetch_assoc($applied)['total'];

// Saved Jobs
$saved = mysqli_query($conn,
"SELECT COUNT(*) AS total FROM saved_jobs WHERE user_id='$user_id'");
$saved = mysqli_fetch_assoc($saved)['total'];

// Interviews
$interviews = mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM interviews i
JOIN applications a
ON i.application_id=a.application_id
WHERE a.applicant_id='$user_id'
");

$interviews = mysqli_fetch_assoc($interviews)['total'];

// Recommended Jobs
$recommended = mysqli_query($conn,
"SELECT COUNT(*) AS total FROM jobs WHERE status='Open'");
$recommended = mysqli_fetch_assoc($recommended)['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Job Seeker Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<link rel="stylesheet"
href="assets/css/jobseeker.css">

</head>

<body>

<?php include("includes/sidebar.php"); ?>

<div class="main-content">

<?php include("includes/topbar.php"); ?>

<div class="container-fluid mt-4">

<div class="row">

<div class="col-md-3">

<div class="card dashboard-card bg-primary text-white">

<div class="card-body">

<i class="bi bi-file-earmark-check display-5"></i>

<h5 class="mt-3">
Applied Jobs
</h5>

<h2>
<?= $applied; ?>
</h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card dashboard-card bg-success text-white">

<div class="card-body">

<i class="bi bi-bookmark-fill display-5"></i>

<h5 class="mt-3">
Saved Jobs
</h5>

<h2>
<?= $saved; ?>
</h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card dashboard-card bg-warning text-dark">

<div class="card-body">

<i class="bi bi-calendar-event display-5"></i>

<h5 class="mt-3">
Interviews
</h5>

<h2>
<?= $interviews; ?>
</h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card dashboard-card bg-info text-white">

<div class="card-body">

<i class="bi bi-briefcase-fill display-5"></i>

<h5 class="mt-3">
Open Jobs
</h5>

<h2>
<?= $recommended; ?>
</h2>

</div>

</div>

</div>

</div>

<hr class="my-4">

<div class="row">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header">

<h5>Recent Applications</h5>

</div>

<div class="card-body">

<table class="table table-striped">

<thead>

<tr>

<th>Job</th>

<th>Status</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php

$query = mysqli_query($conn,"
SELECT jobs.title,
applications.status,
applications.applied_at
FROM applications
JOIN jobs
ON jobs.job_id=applications.job_id
WHERE applicant_id='$user_id'
ORDER BY applied_at DESC
LIMIT 5
");

if(mysqli_num_rows($query)>0){

while($row=mysqli_fetch_assoc($query)){

?>

<tr>

<td><?= htmlspecialchars($row['title']); ?></td>

<td>

<span class="badge bg-primary">

<?= htmlspecialchars($row['status']); ?>

</span>

</td>

<td><?= htmlspecialchars($row['applied_at']); ?></td>

</tr>

<?php

}

}else{

echo "<tr>
<td colspan='3'>
No applications found.
</td>
</tr>";

}

?>

</tbody>

</table>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card shadow">

<div class="card-header">

<h5>Latest Jobs</h5>

</div>

<div class="card-body">

<?php

$jobs=mysqli_query($conn,"
SELECT *
FROM jobs
WHERE status='Open'
ORDER BY created_at DESC
LIMIT 5
");

while($job=mysqli_fetch_assoc($jobs)){

?>

<div class="mb-3">

<h6>

<?= htmlspecialchars($job['title']); ?>

</h6>

<p>

<i class="bi bi-geo-alt"></i>

<?= htmlspecialchars($job['location']); ?>

</p>

<a href="job_details.php?id=<?= $job['job_id']; ?>"
class="btn btn-sm btn-primary">

View Job

</a>

</div>

<hr>

<?php

}

?>

</div>

</div>

</div>

</div>

</div>

<?php include("includes/footer.php"); ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>