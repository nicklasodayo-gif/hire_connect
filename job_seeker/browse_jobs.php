<?php
include("includes/auth.php");

$user_id = $_SESSION['user_id'];

$search = $_GET['search'] ?? "";
$category = $_GET['category'] ?? "";
$type = $_GET['type'] ?? "";
$location = $_GET['location'] ?? "";

$sql = "
SELECT *
FROM jobs
WHERE status='Open'
";

if($search != ""){
    $sql .= " AND title LIKE '%".mysqli_real_escape_string($conn,$search)."%'";
}

if($category != ""){
    $sql .= " AND category='".mysqli_real_escape_string($conn,$category)."'";
}

if($type != ""){
    $sql .= " AND employment_type='".mysqli_real_escape_string($conn,$type)."'";
}

if($location != ""){
    $sql .= " AND location LIKE '%".mysqli_real_escape_string($conn,$location)."%'";
}

$sql .= " ORDER BY created_at DESC";

$jobs = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Browse Jobs</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/jobseeker.css">

</head>

<body>

<?php include("includes/sidebar.php"); ?>

<div class="main-content">

<?php include("includes/topbar.php"); ?>

<div class="container-fluid mt-4">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>

<i class="bi bi-search"></i>

Browse Jobs

</h4>

</div>

<div class="card-body">

<form method="GET">

<div class="row">

<div class="col-md-3">

<input
type="text"
name="search"
class="form-control"
placeholder="Search Job"
value="<?= htmlspecialchars($search); ?>">

</div>

<div class="col-md-2">

<select
name="category"
class="form-select">

<option value="">All Categories</option>

<option <?=($category=="IT")?"selected":"";?>>
IT
</option>

<option <?=($category=="Finance")?"selected":"";?>>
Finance
</option>

<option <?=($category=="Healthcare")?"selected":"";?>>
Healthcare
</option>

<option <?=($category=="Education")?"selected":"";?>>
Education
</option>

<option <?=($category=="Engineering")?"selected":"";?>>
Engineering
</option>

</select>

</div>

<div class="col-md-2">

<select
name="type"
class="form-select">

<option value="">Employment Type</option>

<option <?=($type=="Full-Time")?"selected":"";?>>
Full-Time
</option>

<option <?=($type=="Part-Time")?"selected":"";?>>
Part-Time
</option>

<option <?=($type=="Internship")?"selected":"";?>>
Internship
</option>

<option <?=($type=="Remote")?"selected":"";?>>
Remote
</option>

<option <?=($type=="Contract")?"selected":"";?>>
Contract
</option>

</select>

</div>

<div class="col-md-3">

<input
type="text"
name="location"
class="form-control"
placeholder="Location"
value="<?= htmlspecialchars($location); ?>">

</div>

<div class="col-md-2">

<button
class="btn btn-primary w-100">

<i class="bi bi-search"></i>

Search

</button>

</div>

</div>

</form>

<hr>

<div class="row">

<?php

if(mysqli_num_rows($jobs)>0){

while($job=mysqli_fetch_assoc($jobs)){

$logo="../uploads/logos/default.png";

if(!empty($job['logo'])){

$logo="../uploads/logos/".$job['logo'];

}

?>

<div class="col-lg-4 mb-4">

<div class="card shadow h-100">

<img
src="<?= $logo; ?>"
class="card-img-top"
style="height:180px;object-fit:contain;padding:15px;">

<div class="card-body">

<h5>

<?= htmlspecialchars($job['title']); ?>

</h5>

<p>

<i class="bi bi-tag-fill"></i>

<?= htmlspecialchars($job['category']); ?>

</p>

<p>

<i class="bi bi-geo-alt-fill"></i>

<?= htmlspecialchars($job['location']); ?>

</p>

<p>

<i class="bi bi-clock-fill"></i>

<?= htmlspecialchars($job['employment_type']); ?>

</p>

<p>

<i class="bi bi-cash-stack"></i>

<?= htmlspecialchars($job['salary']); ?>

</p>

<p>

Deadline:

<strong>

<?= htmlspecialchars($job['deadline']); ?>

</strong>

</p>

<p>

<?= substr(strip_tags($job['description']),0,120); ?>...

</p>

</div>

<div class="card-footer text-center">

<a
href="job_details.php?id=<?= $job['job_id']; ?>"
class="btn btn-primary btn-sm">

<i class="bi bi-eye"></i>

View

</a>

<a
href="saved_jobs.php?save=<?= $job['job_id']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-bookmark"></i>

Save

</a>

<a
href="apply_job.php?id=<?= $job['job_id']; ?>"
class="btn btn-success btn-sm">

<i class="bi bi-send"></i>

Apply

</a>

</div>

</div>

</div>

<?php

}

}else{

?>

<div class="alert alert-info">

No jobs available.

</div>

<?php

}

?>

</div>

</div>

</div>

</div>

<?php include("includes/footer.php"); ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>