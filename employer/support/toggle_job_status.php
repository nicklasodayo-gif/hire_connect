<?php
include "includes/auth.php";
include "../connect.php";

$id = (int)$_GET['id'];
$employer_id = $_SESSION['user_id'];

$query = mysqli_query(
    $conn,
    "SELECT status
     FROM jobs
     WHERE job_id='$id'
     AND employer_id='$employer_id'"
);

$job = mysqli_fetch_assoc($query);

$new_status =
($job['status'] == 'Open')
? 'Closed'
: 'Open';

mysqli_query(
    $conn,
    "UPDATE jobs
     SET status='$new_status'
     WHERE job_id='$id'
     AND employer_id='$employer_id'"
);

header("Location: manage_jobs.php");
exit();
?>