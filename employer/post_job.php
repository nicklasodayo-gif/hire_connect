<?php
session_start();
include "connect.php";

if (!isset($_SESSION['employer_id'])) {
    header("Location: login.php ");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $employer_id = $_SESSION['employer_id'];

    $title = htmlspecialchars(trim($_POST['title']));
    $category = htmlspecialchars(trim($_POST['category']));
    $employment_type = htmlspecialchars(trim($_POST['employment_type']));
    $location = htmlspecialchars(trim($_POST['location']));
    $salary = htmlspecialchars(trim($_POST['salary']));
    $deadline = $_POST['deadline'];
    $description = $_POST['description'];
    $requirements = $_POST['requirements'];
    $status = $_POST['status'];

    $logo = "";

    if (!empty($_FILES['logo']['name'])) {

        $allowed = ['jpg','jpeg','png','webp'];

        $extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));

        if (in_array($extension, $allowed)) {

            $logo = uniqid().".".$extension;

            move_uploaded_file(
                $_FILES['logo']['tmp_name'],
                "../uploads/company_logos/".$logo
            );

        } else {

            $message = "<div class='alert alert-danger'>
                        Invalid image format.
                        </div>";
        }
    }

    if(empty($message)){

        $stmt = $conn->prepare("

        INSERT INTO jobs
        (
        employer_id,
        title,
        category,
        employment_type,
        location,
        salary,
        deadline,
        description,
        requirements,
        logo,
        status
        )

        VALUES
        (?,?,?,?,?,?,?,?,?,?,?)

        ");

        $stmt->bind_param(
            "issssssssss",
            $employer_id,
            $title,
            $category,
            $employment_type,
            $location,
            $salary,
            $deadline,
            $description,
            $requirements,
            $logo,
            $status
        );

        if($stmt->execute()){

            $message = "<div class='alert alert-success'>
                        <i class='bi bi-check-circle'></i>
                        Job posted successfully.
                        </div>";

        }else{

            $message = "<div class='alert alert-danger'>
                        ".$stmt->error."
                        </div>";

        }

    }

}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>Post Job</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<style>

body{
background:#eef2f7;
}

.sidebar{
height:100vh;
background:#1f2937;
padding-top:20px;
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

img{
border-radius:10px;
}

</style>

</head>

<body>

<div class="container-fluid">

<div class="row">

<!-- Sidebar -->

<div class="col-md-2 sidebar">

<h4 class="text-center text-white mb-4">
HireConnect
</h4>

<a href="dashboard.php">
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

<a href="applicants.php">
<i class="bi bi-people"></i>
Applicants
</a>

<a href="../logout.php">
<i class="bi bi-box-arrow-right"></i>
Logout
</a>

</div>

<!-- Main Content -->

<div class="col-md-10">

<div class="container py-5">

<div class="card">

<div class="card-header bg-primary text-white">

<h3>

<i class="bi bi-briefcase-fill"></i>

Create New Job

</h3>

</div>

<div class="card-body">

<?= $message ?>

<form method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">

<label>Job Title</label>

<input
type="text"
name="title"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Category</label>

<select
name="category"
class="form-select"
required>

<option>Information Technology</option>
<option>Engineering</option>
<option>Finance</option>
<option>Marketing</option>
<option>Education</option>
<option>Healthcare</option>
<option>Business</option>
<option>Hospitality</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Employment Type</label>

<select
name="employment_type"
class="form-select">

<option>Full-Time</option>
<option>Part-Time</option>
<option>Internship</option>
<option>Contract</option>
<option>Remote</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Job Location</label>

<input
type="text"
name="location"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>Salary</label>

<input
type="text"
name="salary"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>Deadline</label>

<input
type="date"
name="deadline"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Status</label>

<select
name="status"
class="form-select">

<option>Open</option>
<option>Closed</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Company Logo</label>

<input
type="file"
name="logo"
id="logo"
class="form-control">

<br>

<img
id="preview"
width="120"
style="display:none;">

</div>

<div class="col-12 mb-3">

<label>Description</label>

<textarea
id="description"
name="description"></textarea>

</div>

<div class="col-12 mb-3">

<label>Requirements</label>

<textarea
id="requirements"
name="requirements"></textarea>

</div>

<div class="col-12">

<button
class="btn btn-primary btn-lg">

<i class="bi bi-send-fill"></i>

Publish Job

</button>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

</div>

<script>

ClassicEditor
.create(document.querySelector('#description'));

ClassicEditor
.create(document.querySelector('#requirements'));

logo.onchange = evt=>{

const[file]=logo.files;

if(file){

preview.src=URL.createObjectURL(file);

preview.style.display="block";

}

}

</script>

</body>
</html>