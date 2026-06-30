<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/config.php";
require_once "../includes/employer_auth.php";

$user_id = $_SESSION['user_id'];

/*---------------------------------------
GET EMPLOYER ID
---------------------------------------*/

$stmt = $conn->prepare("
SELECT employer_id
FROM employers
WHERE user_id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){
    die("Employer profile not found.");
}

$employer = $result->fetch_assoc();
$employer_id = $employer['employer_id'];

$stmt->close();

/*---------------------------------------
SEARCH
---------------------------------------*/

$search="";

if(isset($_GET['search'])){

    $search=trim($_GET['search']);

    $stmt=$conn->prepare("
    SELECT *
    FROM jobs
    WHERE employer_id=?
    AND title LIKE ?
    ORDER BY created_at DESC
    ");

    $keyword="%".$search."%";

    $stmt->bind_param(
        "is",
        $employer_id,
        $keyword
    );

}else{

    $stmt=$conn->prepare("
    SELECT *
    FROM jobs
    WHERE employer_id=?
    ORDER BY created_at DESC
    ");

    $stmt->bind_param(
        "i",
        $employer_id
    );

}

$stmt->execute();

$jobs=$stmt->get_result();

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Manage Jobs</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">

<style>

body{
background:#eef2f7;
}

.sidebar{
height:100vh;
background:#1f2937;
padding-top:20px;
}

.sidebar h3{
color:white;
text-align:center;
margin-bottom:30px;
}

.sidebar a{
display:block;
padding:15px;
color:white;
text-decoration:none;
}

.sidebar a:hover{
background:#374151;
}

.card{
border:none;
border-radius:15px;
box-shadow:0 5px 20px rgba(0,0,0,.1);
}

table td{
vertical-align:middle;
}

</style>

</head>

<body>

<div class="container-fluid">

<div class="row">

<div class="col-md-2 sidebar">

<h3>HireConnect</h3>

<a href="employer_dashboard.php">
<i class="bi bi-speedometer2"></i>
 Dashboard
</a>

<a href="post_job.php">
<i class="bi bi-plus-circle"></i>
 Post Job
</a>

<a href="manage_jobs.php">
<i class="bi bi-briefcase"></i>
 Manage Jobs
</a>

<a href="view_applicants.php">
<i class="bi bi-people"></i>
 Applicants
</a>

<a href="schedule_interviews.php">
<i class="bi bi-calendar-event"></i>
 Interviews
</a>

<a href="employer_profile.php">
<i class="bi bi-building"></i>
 Company Profile
</a>

<a href="employer_settings.php">
<i class="bi bi-gear"></i>
 Settings
</a>

<a href="../logout.php">
<i class="bi bi-box-arrow-right"></i>
 Logout
</a>

</div>

<div class="col-md-10">

<div class="container py-4">

<div class="card">

<div class="card-header bg-primary text-white">

<div class="d-flex justify-content-between">

<h3>

<i class="bi bi-briefcase-fill"></i>

Manage Jobs

</h3>

<a
href="post_job.php"
class="btn btn-light">

<i class="bi bi-plus-circle"></i>

New Job

</a>

</div>

</div>

<div class="card-body">

<?php
if(isset($_SESSION['message'])){
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<form
method="GET"
class="row mb-3">

<div class="col-md-6">

<input
type="text"
name="search"
value="<?= htmlspecialchars($search); ?>"
class="form-control"
placeholder="Search Job">

</div>

<div class="col-md-2">

<button class="btn btn-primary">

Search

</button>

</div>

</form>

<form
action="bulk_actions.php"
method="POST">

<div class="row mb-3">

<div class="col-md-3">

<select
name="action"
class="form-select">

<option value="">Bulk Action</option>

<option value="open">Open</option>

<option value="close">Close</option>

<option value="pending">Pending Approval</option>

<option value="delete">Delete</option>

</select>

</div>

<div class="col-md-2">

<button class="btn btn-success">

Apply

</button>

</div>

</div>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>

<input
type="checkbox"
id="checkAll">

</th>

<th>Title</th>

<th>Category</th>

<th>Status</th>

<th>Approval</th>

<th>Deadline</th>

<th>Created</th>

<th width="200">

Actions

</th>

</tr>

</thead>

<tbody>

<?php

if($jobs->num_rows>0){

while($row=$jobs->fetch_assoc()){

?>

<tr>

<td>

<input
type="checkbox"
name="job_ids[]"
value="<?= $row['job_id']; ?>">

</td>

<td>

<?= htmlspecialchars($row['title']); ?>

</td>

<td>

<?= htmlspecialchars($row['category']); ?>

</td>

<td>

<?php

if($row['status']=="Open"){

echo "<span class='badge bg-success'>Open</span>";

}else{

echo "<span class='badge bg-danger'>Closed</span>";

}

?>

</td>

<td>

<?php

switch($row['approval_status']){

case "Approved":

echo "<span class='badge bg-success'>Approved</span>";

break;

case "Rejected":

echo "<span class='badge bg-danger'>Rejected</span>";

break;

default:

echo "<span class='badge bg-warning text-dark'>Pending</span>";

}

?>

</td>

<td>

<?= $row['deadline']; ?>

</td>

<td>

<?= $row['created_at']; ?>

</td>

<td>

<a
href="view_job.php?id=<?= $row['job_id']; ?>"
class="btn btn-info btn-sm">

<i class="bi bi-eye"></i>

</a>

<a
href="edit_job.php?id=<?= $row['job_id']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil"></i>

</a>

<a
href="delete_job.php?id=<?= $row['job_id']; ?>"
onclick="return confirm('Delete this job?')"
class="btn btn-danger btn-sm">

<i class="bi bi-trash"></i>

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="8" class="text-center">

No jobs found.

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

</div>

<script>

document.getElementById("checkAll").addEventListener("change",function(){

let boxes=document.querySelectorAll("input[name='job_ids[]']");

boxes.forEach(box=>box.checked=this.checked);

});

</script>

</body>

</html>