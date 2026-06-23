<?php
include "../connect.php";
include "../header.php";

$job_id = $_GET['job_id'];

$result = mysqli_query($conn, "SELECT * FROM applications WHERE job_id='$job_id'");
?>

<div class="applicants">

<h2>Applicants</h2>

<?php while($row = mysqli_fetch_assoc($result)): ?>

    <div class="applicant-card">

        <h3><?php echo $row['applicant_name']; ?></h3>
        <p><?php echo $row['email']; ?></p>

        <a href="../uploads/<?php echo $row['cv']; ?>" download>
            Download CV
        </a>

    </div>

<?php endwhile; ?>

</div>

<style>
.applicants{
    padding:40px;
}

.applicant-card{
    background:white;
    padding:15px;
    margin-bottom:10px;
    border-radius:8px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}
</style>

<?php include "../footer.php"; ?>