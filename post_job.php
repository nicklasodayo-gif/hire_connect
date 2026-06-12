<?php

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $jobTitle = htmlspecialchars($_POST["job_title"]);
    $company = htmlspecialchars($_POST["company"]);
    $location = htmlspecialchars($_POST["location"]);
    $jobType = htmlspecialchars($_POST["job_type"]);
    $salary = htmlspecialchars($_POST["salary"]);
    $description = htmlspecialchars($_POST["description"]);

    $message = "Job '{$jobTitle}' has been posted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job | HireConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f4f6f9;
        }

        .job-card{
            max-width:700px;
            margin:50px auto;
        }
    </style>
</head>
<body>
    
<header>
<a href="index.php" class="floating-home">
    🏠
</a>
</header>

<div class="container">

    <div class="card shadow job-card">

        <div class="card-body">

            <h2 class="text-center mb-4">
                Post a Job
            </h2>

            <?php if(!empty($message)): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Job Title</label>

                    <input
                        type="text"
                        name="job_title"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Company Name</label>

                    <input
                        type="text"
                        name="company"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Location</label>

                    <input
                        type="text"
                        name="location"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Job Type</label>

                    <select
                        name="job_type"
                        class="form-select"
                        required
                    >
                        <option value="">Select Job Type</option>
                        <option>Full-Time</option>
                        <option>Part-Time</option>
                        <option>Contract</option>
                        <option>Internship</option>
                        <option>Remote</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Salary</label>

                    <input
                        type="text"
                        name="salary"
                        class="form-control"
                        placeholder="e.g. KES 80,000"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Job Description
                    </label>

                    <textarea
                        name="description"
                        rows="5"
                        class="form-control"
                        required
                    ></textarea>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100">
                    Post Job
                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>