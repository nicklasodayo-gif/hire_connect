<?php
require_once "../includes/jobseeker_auth.php";
require_once "../config/config.php";

if(!isset($_GET['id'])){
    header("Location: browse_jobs.php");
    exit();
}

$job_id = intval($_GET['id']);

$query = mysqli_query($conn,"
SELECT
jobs.*,
employers.company_name,
employers.website,
employers.industry,
employers.description AS company_description,
users.email

FROM jobs

JOIN employers
ON jobs.employer_id=employers.employer_id

JOIN users
ON employers.employer_id=users.user_id

WHERE jobs.job_id='$job_id'
");

if(mysqli_num_rows($query)==0){

die("Job not found.");

}

$job=mysqli_fetch_assoc($query);

$logo="../uploads/logos/default.png";

if(!empty($job['logo'])){

$logo="../uploads/logos/".$job['logo'];

}
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title><?= htmlspecialchars($job['title']); ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">

<link rel="stylesheet"
href="assets/css/jobseeker.css">

</head>

<body>

<?php include("includes/sidebar.php"); ?>

<div class="main-content">

<?php include("includes/topbar.php"); ?>

<div class="container mt-4">

<div class="row">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3>

<?= htmlspecialchars($job['title']); ?>

</h3>

</div>

<div class="card-body">

<p>

<img
src="<?= $logo; ?>"
style="width:120px;height:120px;object-fit:contain;">

</p>

<h5>

<?= htmlspecialchars($job['company_name']); ?>

</h5>

<hr>

<div class="row">

<div class="col-md-6">

<p>

<strong>Category</strong><br>

<?= htmlspecialchars($job['category']); ?>

</p>

</div>

<div class="col-md-6">

<p>

<strong>Employment Type</strong><br>

<?= htmlspecialchars($job['employment_type']); ?>

</p>

</div>

<div class="col-md-6">

<p>

<strong>Location</strong><br>

<?= htmlspecialchars($job['location']); ?>

</p>

</div>

<div class="col-md-6">

<p>

<strong>Salary</strong><br>

<?= htmlspecialchars($job['salary']); ?>

</p>

</div>

<div class="col-md-6">

<p>

<strong>Deadline</strong><br>

<?= htmlspecialchars($job['deadline']); ?>

</p>

</div>

<div class="col-md-6">

<p>

<strong>Status</strong><br>

<span class="badge bg-success">

<?= htmlspecialchars($job['status']); ?>

</span>

</p>

</div>

</div>

<hr>

<h4>

Job Description

</h4>

<p>

<?= nl2br(htmlspecialchars($job['description'])); ?>

</p>

<hr>

<h4>

Requirements

</h4>

<p>

<?= nl2br(htmlspecialchars($job['requirements'])); ?>

</p>

<hr>

<div class="d-flex gap-2">

<a
href="apply_job.php?id=<?= $job['job_id']; ?>"
class="btn btn-success">

<i class="bi bi-send"></i>

Apply Now

</a>

<a
href="saved_jobs.php?save=<?= $job['job_id']; ?>"
class="btn btn-warning">

<i class="bi bi-bookmark"></i>

Save Job

</a>

<a
href="browse_jobs.php"
class="btn btn-secondary">

Back

</a>

</div>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card shadow">

<div class="card-header bg-dark text-white">

<h5>

Company Information

</h5>

</div>

<div class="card-body">

<h5>

<?= htmlspecialchars($job['company_name']); ?>

</h5>

<p>

<strong>Industry</strong><br>

<?= htmlspecialchars($job['industry']); ?>

</p>

<p>

<strong>Website</strong><br>

<a href="<?= htmlspecialchars($job['website']); ?>"
target="_blank">

<?= htmlspecialchars($job['website']); ?>

</a>

</p>

<p>

<strong>Email</strong><br>

<?= htmlspecialchars($job['email']); ?>

</p>

<hr>

<p>

<?= nl2br(htmlspecialchars($job['company_description'])); ?>

</p>

</div>

</div>

<hr>

<div class="card shadow">

<div class="card-header bg-primary text-white">

Similar Jobs

</div>

<div class="card-body">

<?php

$similar=mysqli_query($conn,"
SELECT job_id,title
FROM jobs
WHERE category='{$job['category']}'
AND job_id!='{$job['job_id']}'
AND status='Open'
LIMIT 5
");

if(mysqli_num_rows($similar)>0){

while($s=mysqli_fetch_assoc($similar)){

?>

<p>

<a href="job_details.php?id=<?= $s['job_id']; ?>">

<?= htmlspecialchars($s['title']); ?>

</a>

</p>

<?php

}

}else{

echo "<p>No similar jobs available.</p>";

}

?>

<?php
$expired = strtotime($job['deadline']) < time();
?>

<?php if($expired){ ?>

<button class="btn btn-danger" disabled>
    Application Closed
</button>

<?php } else { ?>

<a href="apply_job.php?id=<?= $job['job_id']; ?>"
class="btn btn-success">
    Apply Now
</a>

<?php } ?>

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