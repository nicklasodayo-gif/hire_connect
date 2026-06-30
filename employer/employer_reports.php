<?php
require_once "../includes/employer_auth.php";
require_once "../config/config.php";

$employer_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| DASHBOARD REPORTS
|--------------------------------------------------------------------------
*/

// Total Jobs
$result = mysqli_query($conn,"
SELECT COUNT(*) total
FROM jobs
WHERE employer_id='$employer_id'
");
$totalJobs = mysqli_fetch_assoc($result)['total'];

// Open Jobs
$result = mysqli_query($conn,"
SELECT COUNT(*) total
FROM jobs
WHERE employer_id='$employer_id'
AND status='Open'
");
$openJobs = mysqli_fetch_assoc($result)['total'];

// Closed Jobs
$result = mysqli_query($conn,"
SELECT COUNT(*) total
FROM jobs
WHERE employer_id='$employer_id'
AND status='Closed'
");
$closedJobs = mysqli_fetch_assoc($result)['total'];

// Total Applications
$result = mysqli_query($conn,"
SELECT COUNT(*) total
FROM applications
JOIN jobs
ON applications.job_id=jobs.job_id
WHERE jobs.employer_id='$employer_id'
");
$totalApplications = mysqli_fetch_assoc($result)['total'];

// Pending
$result = mysqli_query($conn,"
SELECT COUNT(*) total
FROM applications
JOIN jobs
ON applications.job_id=jobs.job_id
WHERE jobs.employer_id='$employer_id'
AND applications.status='Pending'
");
$pending = mysqli_fetch_assoc($result)['total'];

// Hired
$result = mysqli_query($conn,"
SELECT COUNT(*) total
FROM applications
JOIN jobs
ON applications.job_id=jobs.job_id
WHERE jobs.employer_id='$employer_id'
AND applications.status='Hired'
");
$hired = mysqli_fetch_assoc($result)['total'];

// Job statistics
$jobs = mysqli_query($conn,"
SELECT
jobs.job_id,
jobs.title,
COUNT(applications.application_id) AS applicants
FROM jobs
LEFT JOIN applications
ON jobs.job_id=applications.job_id
WHERE jobs.employer_id='$employer_id'
GROUP BY jobs.job_id
ORDER BY jobs.created_at DESC
");
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Employer Reports</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link rel="stylesheet" href="assets/css/employer.css">

<style>

.card{
border:none;
border-radius:15px;
box-shadow:0 5px 20px rgba(0,0,0,.08);
}

.stat{
font-size:35px;
font-weight:bold;
}

</style>

</head>

<body>

<?php include "includes/sidebar.php"; ?>

<div class="main-content">

<?php include "includes/topbar.php"; ?>

<div class="container-fluid mt-4">

<h2 class="mb-4">

<i class="bi bi-bar-chart-fill"></i>

Employer Reports

</h2>

<div class="row">

<div class="col-md-4 mb-3">

<div class="card text-center p-4">

<h5>Total Jobs</h5>

<div class="stat text-primary">

<?= $totalJobs ?>

</div>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="card text-center p-4">

<h5>Applications</h5>

<div class="stat text-success">

<?= $totalApplications ?>

</div>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="card text-center p-4">

<h5>Hired</h5>

<div class="stat text-warning">

<?= $hired ?>

</div>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="card text-center p-4">

<h5>Pending</h5>

<div class="stat text-info">

<?= $pending ?>

</div>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="card text-center p-4">

<h5>Open Jobs</h5>

<div class="stat text-success">

<?= $openJobs ?>

</div>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="card text-center p-4">

<h5>Closed Jobs</h5>

<div class="stat text-danger">

<?= $closedJobs ?>

</div>

</div>

</div>

</div>

<div class="card mt-4">

<div class="card-header bg-primary text-white">

Job Overview

</div>

<div class="card-body">

<canvas id="reportChart" height="90"></canvas>

</div>

</div>

<div class="card mt-4">

<div class="card-header bg-dark text-white">

Recent Job Statistics

</div>

<div class="card-body">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>Job Title</th>

<th>Applicants</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($jobs)){ ?>

<tr>

<td><?= htmlspecialchars($row['title']) ?></td>

<td><?= $row['applicants'] ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<?php include "includes/footer.php"; ?>

</div>

<script>

new Chart(document.getElementById('reportChart'),{

type:'bar',

data:{

labels:[
'Jobs',
'Applications',
'Pending',
'Hired',
'Open',
'Closed'
],

datasets:[{

label:'Employer Report',

data:[
<?= $totalJobs ?>,
<?= $totalApplications ?>,
<?= $pending ?>,
<?= $hired ?>,
<?= $openJobs ?>,
<?= $closedJobs ?>
]

}]

},

options:{

responsive:true,

plugins:{
legend:{
display:false
}
},

scales:{
y:{
beginAtZero:true
}
}

}

});

</script>

</body>

</html>