<?php
include "../connect.php";
include "../header.php";

$employer_id = 1;

$result = mysqli_query($conn, "SELECT * FROM jobs WHERE employer_id='$employer_id'");
?>

<div class="jobs">

<h2>My Posted Jobs</h2>

<?php while($row = mysqli_fetch_assoc($result)): ?>

    <div class="job-card">

        <h3><?php echo $row['title']; ?></h3>
        <p><?php echo $row['description']; ?></p>
        <p><b>Location:</b> <?php echo $row['location']; ?></p>

        <a href="view_applicants.php?job_id=<?php echo $row['id']; ?>">
            View Applicants
        </a>

    </div>

<?php endwhile; ?>

</div>

<style>
.jobs{
    padding:40px;
}

.job-card{
    background:#f4f6f9;
    padding:20px;
    margin-bottom:15px;
    border-radius:10px;
}
</style>

<?php include "../footer.php"; ?>