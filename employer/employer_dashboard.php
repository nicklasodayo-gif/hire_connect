<?php
include "includes/auth.php";

$employer_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| DASHBOARD STATISTICS
|--------------------------------------------------------------------------
*/

$total_jobs = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT job_id
        FROM jobs
        WHERE employer_id='$employer_id'"
    )
);

$open_jobs = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT job_id
        FROM jobs
        WHERE employer_id='$employer_id'
        AND status='Open'"
    )
);

$total_applicants = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT a.application_id

        FROM applications a

        INNER JOIN jobs j
        ON a.job_id = j.job_id

        WHERE j.employer_id='$employer_id'"
    )
);

$shortlisted = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT a.application_id

        FROM applications a

        INNER JOIN jobs j
        ON a.job_id=j.job_id

        WHERE j.employer_id='$employer_id'
        AND a.status='Shortlisted'"
    )
);

/*
|--------------------------------------------------------------------------
| RECENT JOBS
|--------------------------------------------------------------------------
*/

$recent_jobs = mysqli_query(
    $conn,
    "SELECT *
    FROM jobs
    WHERE employer_id='$employer_id'
    ORDER BY created_at DESC
    LIMIT 5"
);

/*
|--------------------------------------------------------------------------
| RECENT APPLICATIONS
|--------------------------------------------------------------------------
*/

// 1. Recent applications
$recent_applications = mysqli_query(
    $conn,
    "SELECT
        a.application_id,
        a.status,
        a.applied_at,
        u.full_name,
        j.title
    FROM applications a
    INNER JOIN users u ON a.applicant_id = u.user_id
    INNER JOIN jobs j ON a.job_id = j.job_id
    ORDER BY a.applied_at DESC
    LIMIT 5"
);

// 2. Employer jobs (prepared statement)
$sql = "SELECT * FROM jobs j WHERE j.employer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employer_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Employer Dashboard | HireConnect</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link rel="stylesheet"
href="employer\assets\dashboard.css">

</head>

<body>

<?php include "includes/sidebar.php"; ?>

<div class="main-content">

    <?php include "includes/topbar.php"; ?>

    <div class="page-content">

        <div class="container-fluid">

            <!-- Welcome -->

            <div class="mb-4">

                <h2>
                    Welcome Back 👋
                </h2>

                <p class="text-muted">
                    Manage jobs, applicants and hiring activities.
                </p>

            </div>

            <!-- Statistics -->
            
            <div class="row">

                <div class="col-lg-3 col-md-6 mb-4">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="text-muted">
                                        Total Jobs
                                    </h6>

                                    <h2>
                                        <?= $total_jobs ?>
                                    </h2>

                                </div>

                                <i class="bi bi-briefcase-fill fs-1 text-primary"></i>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6 mb-4">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="text-muted">
                                        Open Jobs
                                    </h6>

                                    <h2>
                                        <?= $open_jobs ?>
                                    </h2>

                                </div>

                                <i class="bi bi-check-circle-fill fs-1 text-success"></i>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6 mb-4">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="text-muted">
                                        Applicants
                                    </h6>

                                    <h2>
                                        <?= $total_applicants ?>
                                    </h2>

                                </div>

                                <i class="bi bi-people-fill fs-1 text-warning"></i>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6 mb-4">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="text-muted">
                                        Shortlisted
                                    </h6>

                                    <h2>
                                        <?= $shortlisted ?>
                                    </h2>

                                </div>

                                <i class="bi bi-person-check-fill fs-1 text-danger"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Quick Actions -->

            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <h5 class="mb-0">
                        Quick Actions
                    </h5>

                </div>

                <div class="card-body">

                    <a href="post_job.php"
                    class="btn btn-primary me-2">

                        <i class="bi bi-plus-circle"></i>
                        Post Job

                    </a>

                    <a href="manage_jobs.php"
                    class="btn btn-success me-2">

                        <i class="bi bi-briefcase"></i>
                        Manage Jobs

                    </a>

                    <a href="view_applicants.php"
                    class="btn btn-warning text-white">

                        <i class="bi bi-people"></i>
                        Applicants

                    </a>

                </div>

            </div>

            <!-- Recent Jobs -->

            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <h5 class="mb-0">
                        Recent Jobs
                    </h5>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover">

                            <thead>

                            <tr>

                                <th>Job Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Deadline</th>

                            </tr>

                            </thead>

                            <tbody>

                            <?php while($job = mysqli_fetch_assoc($recent_jobs)){ ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($job['title']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($job['category']) ?>
                                    </td>

                                    <td>

                                        <?php if($job['status']=="Open"){ ?>

                                            <span class="badge bg-success">
                                                Open
                                            </span>

                                        <?php } else { ?>

                                            <span class="badge bg-danger">
                                                Closed
                                            </span>

                                        <?php } ?>

                                    </td>

                                    <td>
                                        <?= $job['deadline'] ?>
                                    </td>

                                </tr>

                            <?php } ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <!-- Recent Applications -->

            <div class="card shadow-sm">

                <div class="card-header">

                    <h5 class="mb-0">
                        Recent Applications
                    </h5>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-striped">

                            <thead>

                            <tr>

                                <th>Applicant</th>
                                <th>Job</th>
                                <th>Status</th>
                                <th>Date</th>

                            </tr>

                            </thead>

                            <tbody>

                            <?php while($app = mysqli_fetch_assoc($recent_applications)){ ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($app['fullname']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($app['title']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($app['status']) ?>
                                    </td>

                                    <td>
                                        <?= date(
                                            "d M Y",
                                            strtotime($app['applied_at'])
                                        ) ?>
                                    </td>

                                </tr>

                            <?php } ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <?php include "includes/footer.php"; ?>

</div>

</body>
</html>