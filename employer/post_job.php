<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../config/config.php";
require_once "../includes/employer_auth.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $employer_id      = $_SESSION['user_id'];

    $title            = trim($_POST['title']);
    $category         = trim($_POST['category']);
    $employment_type  = trim($_POST['employment_type']);
    $location         = trim($_POST['location']);
    $salary           = trim($_POST['salary']);
    $deadline         = $_POST['deadline'];
    $description      = $_POST['description'];
    $requirements     = $_POST['requirements'];
    $status           = $_POST['status'];

    if (
        empty($title) ||
        empty($category) ||
        empty($employment_type) ||
        empty($deadline) ||
        empty($description)
    ) {

        $message = "<div class='alert alert-danger'>
                        Please fill all required fields.
                    </div>";

    } else {

        $logo = "";

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {

            $allowed = ['jpg','jpeg','png','webp'];

            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));

            if (in_array($ext,$allowed)) {

                $upload_dir = "../uploads/company_logos/";

                if(!is_dir($upload_dir)){
                    mkdir($upload_dir,0777,true);
                }

                $logo = uniqid().".".$ext;

                move_uploaded_file(
                    $_FILES['logo']['tmp_name'],
                    $upload_dir.$logo
                );

            } else {

                $message = "<div class='alert alert-danger'>
                                Invalid logo format.
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

            if ($stmt->execute()) {

                $message = "
                <div class='alert alert-success'>
                    <i class='bi bi-check-circle-fill'></i>
                    Job posted successfully.
                </div>";

            } else {

                $message = "
                <div class='alert alert-danger'>
                    Failed to post job.<br>
                    Error: " . $stmt->error . "
                </div>";

            }

            $stmt->close();

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Post Job</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<style>

body{
background:#f4f6f9;
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

#preview{
border-radius:10px;
display:none;
margin-top:10px;
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

<div class="container py-5">

<div class="card">

<div class="card-header bg-primary text-white">

<h3>
<i class="bi bi-briefcase-fill"></i>
Post New Job
</h3>

</div>

<div class="card-body">

<?= $message ?>

<form method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">
<label>Job Title</label>
<input type="text" name="title" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Category</label>
<select name="category" class="form-select" required>
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
<select name="employment_type" class="form-select">
<option>Full-Time</option>
<option>Part-Time</option>
<option>Internship</option>
<option>Contract</option>
<option>Remote</option>
</select>
</div>

<div class="col-md-6 mb-3">
<label>Location</label>
<input type="text" name="location" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Salary</label>
<input type="text" name="salary" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Application Deadline</label>
<input type="date" name="deadline" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Status</label>
<select name="status" class="form-select">
<option>Open</option>
<option>Closed</option>
</select>
</div>

<div class="col-md-6 mb-3">
<label>Company Logo</label>
<input type="file"
name="logo"
id="logo"
accept=".jpg,.jpeg,.png,.webp"
class="form-control">

<img id="preview" width="120">
</div>

<div class="col-12 mb-3">
<label>Description</label>
<textarea id="description" name="description"></textarea>
</div>

<div class="col-12 mb-3">
<label>Requirements</label>
<textarea id="requirements" name="requirements"></textarea>
</div>

<div class="col-12">

<button type="submit" class="btn btn-primary btn-lg">
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

ClassicEditor.create(document.querySelector("#description"));
ClassicEditor.create(document.querySelector("#requirements"));

const logo=document.getElementById("logo");
const preview=document.getElementById("preview");

logo.addEventListener("change",function(){

const file=this.files[0];

if(file){

preview.src=URL.createObjectURL(file);
preview.style.display="block";

}

});

</script>

</body>

</html>