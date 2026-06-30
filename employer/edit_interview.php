<?php
session_start();

require_once "../includes/employer_auth.php";
require_once "../config/config.php";

if (!isset($_GET['id'])) {
    header("Location: interview_list.php");
    exit();
}

$interview_id = intval($_GET['id']);

/* Fetch Interview */
$stmt = $conn->prepare("
SELECT
    interviews.*,
    applications.application_id,
    users.full_name,
    users.email,
    jobs.title

FROM interviews

JOIN applications
ON interviews.application_id = applications.application_id

JOIN users
ON applications.user_id = users.user_id

JOIN jobs
ON applications.job_id = jobs.job_id

WHERE interviews.interview_id = ?
");

$stmt->bind_param("i", $interview_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Interview not found.");
}

$interview = $result->fetch_assoc();

/* Update Interview */

if(isset($_POST['update'])){

    $date = $_POST['interview_date'];
    $time = $_POST['interview_time'];
    $type = $_POST['interview_type'];
    $venue = trim($_POST['venue']);
    $meeting = trim($_POST['meeting_link']);
    $notes = trim($_POST['notes']);
    $status = $_POST['status'];

    $update = $conn->prepare("
    UPDATE interviews

    SET

    interview_date=?,
    interview_time=?,
    interview_type=?,
    venue=?,
    meeting_link=?,
    notes=?,
    status=?

    WHERE interview_id=?

    ");

    $update->bind_param(

        "sssssssi",

        $date,
        $time,
        $type,
        $venue,
        $meeting,
        $notes,
        $status,
        $interview_id

    );

    if($update->execute()){

    // Synchronize application status
    $applicationStatus = "Interview Scheduled";

    if($status == "Completed"){
        $applicationStatus = "Interview Completed";
    }
    elseif($status == "Cancelled"){
        $applicationStatus = "Shortlisted";
    }

    $updateApplication = $conn->prepare("
        UPDATE applications
        SET status = ?
        WHERE application_id = ?
    ");

    $updateApplication->bind_param(
        "si",
        $applicationStatus,
        $interview['application_id']
    );

    $updateApplication->execute();

    echo "<div class='alert alert-success'>
        Interview updated successfully.
    </div>";

}

}
?>

<!DOCTYPE html>

<html>

<head>

<meta charset="utf-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>Edit Interview</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">

<style>

body{

background:#f4f6f9;

}

.card{

border:none;

border-radius:15px;

}

</style>

</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>

<i class="bi bi-pencil-square"></i>

Edit Interview

</h4>

</div>

<div class="card-body">

<h5>

<?= htmlspecialchars($interview['full_name']); ?>

</h5>

<p class="text-muted">

<?= htmlspecialchars($interview['title']); ?>

</p>

<hr>

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label>Date</label>

<input
type="date"
name="interview_date"
class="form-control"
required
value="<?= $interview['interview_date']; ?>">

</div>

<div class="col-md-6 mb-3">

<label>Time</label>

<input
type="time"
name="interview_time"
class="form-control"
required
value="<?= $interview['interview_time']; ?>">

</div>

</div>

<div class="mb-3">

<label>Interview Type</label>

<select
name="interview_type"
class="form-select">

<option
<?=($interview['interview_type']=="Physical")?"selected":"";?>>

Physical

</option>

<option
<?=($interview['interview_type']=="Online")?"selected":"";?>>

Online

</option>

</select>

</div>

<div class="mb-3">

<label>Venue</label>

<input
type="text"
name="venue"
class="form-control"
value="<?= htmlspecialchars($interview['venue']); ?>">

</div>

<div class="mb-3">

<label>Meeting Link</label>

<input
type="url"
name="meeting_link"
class="form-control"
value="<?= htmlspecialchars($interview['meeting_link']); ?>">

</div>

<div class="mb-3">

<label>Notes</label>

<textarea
name="notes"
rows="5"
class="form-control"><?= htmlspecialchars($interview['notes']); ?></textarea>

</div>

<div class="mb-4">

<label>Status</label>

<select
name="status"
class="form-select">

<option <?=($interview['status']=="Scheduled")?"selected":"";?>>

Scheduled

</option>

<option <?=($interview['status']=="Completed")?"selected":"";?>>

Completed

</option>

<option <?=($interview['status']=="Cancelled")?"selected":"";?>>

Cancelled

</option>

</select>

</div>

<div class="d-flex justify-content-between">

<a
href="interview_calendar.php"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Back

</a>

<div>

<button
type="submit"
name="update"
class="btn btn-success">

<i class="bi bi-check-circle"></i>

Save Changes

</button>

<a
href="delete_interview.php?id=<?= $interview_id; ?>"
class="btn btn-danger"
onclick="return confirm('Delete this interview?');">

<i class="bi bi-trash"></i>

Delete

</a>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>