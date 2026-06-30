<?php
require_once "../includes/jobseeker_auth.php";
require_once "../config/config.php";

$user_id = $_SESSION['user_id'];
$message = "";

/* ============================
   SAVE JOB
============================ */

if(isset($_GET['save'])){

    $job_id = intval($_GET['save']);

    $check = mysqli_query($conn,"
        SELECT *
        FROM saved_jobs
        WHERE user_id='$user_id'
        AND job_id='$job_id'
    ");

    if(mysqli_num_rows($check)==0){

        mysqli_query($conn,"
            INSERT INTO saved_jobs(
                user_id,
                job_id
            )
            VALUES(
                '$user_id',
                '$job_id'
            )
        ");

        $message = "
        <div class='alert alert-success'>
            Job saved successfully.
        </div>";

    }else{

        $message = "
        <div class='alert alert-warning'>
            Job already saved.
        </div>";

    }

}

/* ============================
   REMOVE SAVED JOB
============================ */

if(isset($_GET['remove'])){

    $saved_id = intval($_GET['remove']);

    mysqli_query($conn,"
        DELETE FROM saved_jobs
        WHERE saved_id='$saved_id'
        AND user_id='$user_id'
    ");

    $message = "
    <div class='alert alert-success'>
        Job removed successfully.
    </div>";

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Saved Jobs</title>

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

<div class="card shadow">

<div class="card-header bg-warning">

<h4>

<i class="bi bi-bookmark-fill"></i>

Saved Jobs

</h4>

</div>

<div class="card-body">

<?= $message; ?>

<?php

$query = mysqli_query($conn,"
SELECT
saved_jobs.saved_id,
saved_jobs.saved_at,
jobs.*

FROM saved_jobs

JOIN jobs
ON saved_jobs.job_id=jobs.job_id

WHERE saved_jobs.user_id='$user_id'

ORDER BY saved_jobs.saved_at DESC
");

if(mysqli_num_rows($query)>0){

?>

<table class="table table-hover">

<thead>

<tr>

<th>Job</th>

<th>Category</th>

<th>Location</th>

<th>Salary</th>

<th>Deadline</th>

<th>Actions</th>

</tr>

</thead>

<tbody>

<?php

while($row=mysqli_fetch_assoc($query)){

?>

<tr>

<td>

<strong>

<?= htmlspecialchars($row['title']); ?>

</strong>

</td>

<td>

<?= htmlspecialchars($row['category']); ?>

</td>

<td>

<?= htmlspecialchars($row['location']); ?>

</td>

<td>

<?= htmlspecialchars($row['salary']); ?>

</td>

<td>

<?= htmlspecialchars($row['deadline']); ?>

</td>

<td>

<a
href="job_details.php?id=<?= $row['job_id']; ?>"
class="btn btn-primary btn-sm">

<i class="bi bi-eye"></i>

View

</a>

<a
href="apply_job.php?id=<?= $row['job_id']; ?>"
class="btn btn-success btn-sm">

<i class="bi bi-send"></i>

Apply

</a>

<a
href="?remove=<?= $row['saved_id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Remove this saved job?')">

<i class="bi bi-trash"></i>

Remove

</a>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

<?php

}else{

?>

<div class="alert alert-info">

<h5>No Saved Jobs</h5>

<p>

You haven't saved any jobs yet.

</p>

<a
href="browse_jobs.php"
class="btn btn-primary">

Browse Jobs

</a>

</div>

<?php

}

?>

</div>

</div>

</div>

<?php include("includes/footer.php"); ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>