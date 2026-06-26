<?php
include("includes/auth.php");

$user_id = $_SESSION['user_id'];

$message = "";

/* =============================
   Withdraw Application
============================= */

if(isset($_GET['withdraw'])){

    $application_id = intval($_GET['withdraw']);

    mysqli_query($conn,"
        DELETE FROM applications
        WHERE application_id='$application_id'
        AND applicant_id='$user_id'
        AND status='Pending'
    ");

    $message = "
    <div class='alert alert-success'>
        Application withdrawn successfully.
    </div>";

}

/* =============================
   Filter
============================= */

$status = $_GET['status'] ?? "";

$sql = "
SELECT
applications.*,
jobs.title,
jobs.location,
employers.company_name,
interviews.interview_date

FROM applications

JOIN jobs
ON applications.job_id = jobs.job_id

JOIN employers
ON jobs.employer_id = employers.employer_id

LEFT JOIN interviews
ON interviews.application_id = applications.application_id

WHERE applications.applicant_id='$user_id'
";

if($status!=""){
    $status = mysqli_real_escape_string($conn,$status);
    $sql .= " AND applications.status='$status'";
}

$sql .= " ORDER BY applications.applied_at DESC";

$applications = mysqli_query($conn,$sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>My Applications</title>

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

<div class="card-header bg-primary text-white d-flex justify-content-between">

<h4>

<i class="bi bi-file-earmark-text"></i>

My Applications

</h4>

<form method="GET">

<select
name="status"
class="form-select"
onchange="this.form.submit()">

<option value="">All Status</option>

<option value="Pending"
<?=($status=="Pending")?"selected":"";?>>

Pending

</option>

<option value="Reviewed"
<?=($status=="Reviewed")?"selected":"";?>>

Reviewed

</option>

<option value="Shortlisted"
<?=($status=="Shortlisted")?"selected":"";?>>

Shortlisted

</option>

<option value="Rejected"
<?=($status=="Rejected")?"selected":"";?>>

Rejected

</option>

<option value="Hired"
<?=($status=="Hired")?"selected":"";?>>

Hired

</option>

</select>

</form>

</div>

<div class="card-body">

<?= $message; ?>

<?php

if(mysqli_num_rows($applications)>0){

?>

<table class="table table-hover align-middle">

<thead>

<tr>

<th>Job</th>

<th>Company</th>

<th>Location</th>

<th>Status</th>

<th>Applied</th>

<th>Interview</th>

<th>Resume</th>

<th>Actions</th>

</tr>

</thead>

<tbody>

<?php

while($row=mysqli_fetch_assoc($applications)){

switch($row['status']){

case "Pending":
$badge="warning";
break;

case "Reviewed":
$badge="info";
break;

case "Shortlisted":
$badge="primary";
break;

case "Rejected":
$badge="danger";
break;

case "Hired":
$badge="success";
break;

default:
$badge="secondary";
}

?>

<tr>

<td>

<strong>

<?= htmlspecialchars($row['title']); ?>

</strong>

</td>

<td>

<?= htmlspecialchars($row['company_name']); ?>

</td>

<td>

<?= htmlspecialchars($row['location']); ?>

</td>

<td>

<span class="badge bg-<?= $badge; ?>">

<?= htmlspecialchars($row['status']); ?>

</span>

</td>

<td>

<?= htmlspecialchars($row['applied_at']); ?>

</td>

<td>

<?php

if(!empty($row['interview_date'])){

echo date("d M Y H:i",
strtotime($row['interview_date']));

}else{

echo "-";

}

?>

</td>

<td>

<?php

if(!empty($row['cv_file'])){

?>

<a
href="../uploads/resumes/<?= htmlspecialchars($row['cv_file']); ?>"
target="_blank"
class="btn btn-sm btn-outline-primary">

<i class="bi bi-file-earmark-pdf"></i>

View

</a>

<?php

}else{

echo "-";

}

?>

</td>

<td>

<a
href="job_details.php?id=<?= $row['job_id']; ?>"
class="btn btn-primary btn-sm">

<i class="bi bi-eye"></i>

</a>

<?php

if($row['status']=="Pending"){

?>

<a
href="?withdraw=<?= $row['application_id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Withdraw this application?');">

<i class="bi bi-trash"></i>

</a>

<?php

}

?>

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

<h5>No Applications Found</h5>

<p>

You haven't applied for any jobs yet.

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


<?php
mysqli_query($conn,"
UPDATE applications
SET status='Withdrawn'
WHERE application_id='$application_id'
AND applicant_id='$user_id'
AND status='Pending'
");
?>

</div>

</div>

</div>

<?php include("includes/footer.php"); ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>