<?php include "../header.php"; ?>

<div class="dashboard">

    <h1>Employer Dashboard</h1>

    <div class="cards">

        <a href="post_job.php" class="card">
            ➕ Post a Job
        </a>

        <a href="my_jobs.php" class="card">
            📄 My Jobs
        </a>

    </div>

</div>

<style>
.dashboard{
    padding:40px;
    text-align:center;
}

.cards{
    display:flex;
    justify-content:center;
    gap:20px;
    margin-top:30px;
}

.card{
    padding:20px;
    background:#0d6efd;
    color:white;
    text-decoration:none;
    border-radius:10px;
    width:200px;
}
</style>

<?php include "../footer.php"; ?>