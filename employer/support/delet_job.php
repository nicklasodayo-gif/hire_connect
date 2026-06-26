<?php
include "includes/auth.php";
include "../connect.php";

$id = (int)$_GET['id'];
$employer_id = $_SESSION['user_id'];

mysqli_query(
    $conn,
    "DELETE FROM jobs
     WHERE job_id='$id'
     AND employer_id='$employer_id'"
);

header("Location: manage_jobs.php");
exit();
?>