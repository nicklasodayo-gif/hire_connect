<?php
include("includes/auth.php");

$user_id = $_SESSION['user_id'];
$message = "";

// Check Job ID
if(!isset($_GET['id'])){
    header("Location: browse_jobs.php");
    exit();
}

$job_id = intval($_GET['id']);

// Get Job Details
$jobQuery = mysqli_query($conn,"
SELECT jobs.*, employers.company_name
FROM jobs
JOIN employers
ON jobs.employer_id = employers.employer_id
WHERE jobs.job_id='$job_id'
");

if(mysqli_num_rows($jobQuery)==0){
    die("Job not found.");
}

$job = mysqli_fetch_assoc($jobQuery);

// Prevent Duplicate Application
$check = mysqli_query($conn,"
SELECT *
FROM applications
WHERE applicant_id='$user_id'
AND job_id='$job_id'
");

if(mysqli_num_rows($check)>0){
    $alreadyApplied = true;
}else{
    $alreadyApplied = false;
}

// Submit Application
if(isset($_POST['apply']) && !$alreadyApplied){

    $cover_letter = mysqli_real_escape_string(
        $conn,
        $_POST['cover_letter']
    );

    $cv_file = "";

    if(isset($_FILES['cv']) && $_FILES['cv']['error']==0){

        $allowed = ['pdf'];

        $extension = strtolower(
            pathinfo(
                $_FILES['cv']['name'],
                PATHINFO_EXTENSION
            )
        );

        if(in_array($extension,$allowed)){

            $cv_file = time()."_".$_FILES['cv']['name'];

            move_uploaded_file(
                $_FILES['cv']['tmp_name'],
                "../uploads/resumes/".$cv_file
            );

        }else{

            $message = "<div class='alert alert-danger'>
            Only PDF files are allowed.
            </div>";

        }

    }

    if($message==""){

        mysqli_query($conn,"
        INSERT INTO applications(

            job_id,
            applicant_id,
            cv_file,
            cover_letter,
            status

        )

        VALUES(

            '$job_id',
            '$user_id',
            '$cv_file',
            '$cover_letter',
            'Pending'

        )
        ");

        $message = "<div class='alert alert-success'>
        Application submitted successfully.
        </div>";

        $alreadyApplied = true;
    }

}
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Apply Job</title>

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

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>

<i class="bi bi-send-check"></i>

Apply for Job

</h3>

</div>

<div class="card-body">

<?= $message; ?>

<h4>

<?= htmlspecialchars($job['title']); ?>

</h4>

<p>

<strong>Company:</strong>

<?= htmlspecialchars($job['company_name']); ?>

</p>

<p>

<strong>Location:</strong>

<?= htmlspecialchars($job['location']); ?>

</p>

<p>

<strong>Employment Type:</strong>

<?= htmlspecialchars($job['employment_type']); ?>

</p>

<p>

<strong>Deadline:</strong>

<?= htmlspecialchars($job['deadline']); ?>

</p>

<hr>

<?php if($alreadyApplied){ ?>

<div class="alert alert-warning">

You have already applied for this job.

</div>

<a href="my_applications.php"
class="btn btn-primary">

View My Applications

</a>

<?php }else{ ?>

<form
method="POST"
enctype="multipart/form-data">

<div class="mb-3">

<label class="form-label">

Upload CV (PDF)

</label>

<input
type="file"
name="cv"
accept=".pdf"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Cover Letter

</label>

<textarea
name="cover_letter"
rows="8"
class="form-control"
placeholder="Introduce yourself and explain why you're the best candidate..."
required></textarea>

</div>

<button
type="submit"
name="apply"
class="btn btn-success">

<i class="bi bi-send-fill"></i>

Submit Application

</button>

<a
href="job_details.php?id=<?= $job_id; ?>"
class="btn btn-secondary">

Cancel

</a>

</form>

<?php } ?>

</div>

</div>

</div>

<?php include("includes/footer.php"); ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>