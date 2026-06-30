<?php
require_once "../includes/admin_auth.php";
require_once "../config/config.php";
include "includes/header.php";
include "includes/sidebar.php";

/* ==========================================================
   DASHBOARD COUNTS
========================================================== */

/**
 * Returns the number of rows from a query.
 */
function getCount(mysqli $conn, string $sql): int
{
    $result = $conn->query($sql);

    return ($result)
        ? (int)$result->fetch_assoc()['total']
        : 0;
}

/* ==========================================================
   SUMMARY CARDS
========================================================== */

$totalUsers = getCount($conn,"
SELECT COUNT(*) total
FROM users
");

$totalEmployers = getCount($conn,"
SELECT COUNT(*) total
FROM users
WHERE role='employer'
");

$totalApplicants = getCount($conn,"
SELECT COUNT(*) total
FROM users
WHERE role='jobseeker'
");

$totalJobs = getCount($conn,"
SELECT COUNT(*) total
FROM jobs
");

$openJobs = getCount($conn,"
SELECT COUNT(*) total
FROM jobs
WHERE status='Open'
");

$closedJobs = getCount($conn,"
SELECT COUNT(*) total
FROM jobs
WHERE status='Closed'
");

$totalApplications = getCount($conn,"
SELECT COUNT(*) total
FROM applications
");

$totalInterviews = getCount($conn,"
SELECT COUNT(*) total
FROM interviews
");

$totalHired = getCount($conn,"
SELECT COUNT(*) total
FROM applications
WHERE status='Hired'
");

$totalRejected = getCount($conn,"
SELECT COUNT(*) total
FROM applications
WHERE status='Rejected'
");

/* ==========================================================
   CARD DEFINITIONS
========================================================== */

$cards = [

[
"title"=>"Users",
"value"=>$totalUsers,
"icon"=>"people-fill",
"color"=>"primary"
],

[
"title"=>"Employers",
"value"=>$totalEmployers,
"icon"=>"building",
"color"=>"success"
],

[
"title"=>"Job Seekers",
"value"=>$totalApplicants,
"icon"=>"person-workspace",
"color"=>"info"
],

[
"title"=>"Jobs",
"value"=>$totalJobs,
"icon"=>"briefcase-fill",
"color"=>"warning"
],

[
"title"=>"Open Jobs",
"value"=>$openJobs,
"icon"=>"folder-check",
"color"=>"success"
],

[
"title"=>"Closed Jobs",
"value"=>$closedJobs,
"icon"=>"folder-x",
"color"=>"secondary"
],

[
"title"=>"Applications",
"value"=>$totalApplications,
"icon"=>"file-earmark-text",
"color"=>"primary"
],

[
"title"=>"Interviews",
"value"=>$totalInterviews,
"icon"=>"calendar-check",
"color"=>"info"
],

[
"title"=>"Hired",
"value"=>$totalHired,
"icon"=>"check-circle-fill",
"color"=>"success"
],

[
"title"=>"Rejected",
"value"=>$totalRejected,
"icon"=>"x-circle-fill",
"color"=>"danger"
]

];

/* ==========================================================
   MONTHLY USER REGISTRATIONS
========================================================== */

$userMonths = [];
$userTotals = [];

$result = $conn->query("
SELECT
    DATE_FORMAT(created_at,'%b') AS month,
    COUNT(*) AS total
FROM users
GROUP BY MONTH(created_at)
ORDER BY MONTH(created_at)
");

while($row = $result->fetch_assoc()){

    $userMonths[] = $row['month'];
    $userTotals[] = (int)$row['total'];

}

/* ==========================================================
   APPLICATION STATUS CHART
========================================================== */

$statusLabels = [];
$statusTotals = [];

$result = $conn->query("
SELECT
    status,
    COUNT(*) total
FROM applications
GROUP BY status
");

while($row = $result->fetch_assoc()){

    $statusLabels[] = $row['status'];
    $statusTotals[] = (int)$row['total'];

}

/* ==========================================================
   JOB CATEGORY CHART
========================================================== */

$categoryLabels = [];
$categoryTotals = [];

$result = $conn->query("
SELECT
    category,
    COUNT(*) total
FROM jobs
GROUP BY category
");

while($row = $result->fetch_assoc()){

    $categoryLabels[] = $row['category'];
    $categoryTotals[] = (int)$row['total'];

}

/* ==========================================================
   RECENT USERS
========================================================== */

$recentUsers = $conn->query("
SELECT
    full_name,
    email,
    role,
    created_at
FROM users
ORDER BY created_at DESC
LIMIT 5
");

/* ==========================================================
   RECENT JOBS
========================================================== */

$recentJobs = $conn->query("
SELECT
    title,
    category,
    status,
    created_at
FROM jobs
ORDER BY created_at DESC
LIMIT 5
");

/* ==========================================================
   RECENT APPLICATIONS
========================================================== */

$recentApplications = $conn->query("
SELECT
    u.full_name,
    j.title,
    a.status,
    a.applied_at
FROM applications a
JOIN users u
    ON u.user_id = a.applicant_id
JOIN jobs j
    ON j.job_id = a.job_id
ORDER BY a.applied_at DESC
LIMIT 5
");

/* ==========================================================
   UPCOMING INTERVIEWS
========================================================== */

$recentInterviews = $conn->query("
SELECT
    u.full_name,
    j.title,
    i.interview_date,
    i.interview_time,
    i.interview_type
FROM interviews i
JOIN applications a
    ON a.application_id = i.application_id
JOIN users u
    ON u.user_id = a.applicant_id
JOIN jobs j
    ON j.job_id = a.job_id
ORDER BY i.interview_date ASC,
         i.interview_time ASC
LIMIT 5
");
?>

<div class="container-fluid py-4">

    <!-- ==========================================
         Dashboard Header
    =========================================== -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="mb-1">
                <i class="bi bi-speedometer2"></i>
                Admin Dashboard
            </h2>

            <p class="text-muted mb-0">
                Monitor users, jobs, applications and interviews.
            </p>

        </div>

        <div>

            <a href="users.php" class="btn btn-primary">
                <i class="bi bi-people-fill"></i>
                Users
            </a>

            <a href="jobs.php" class="btn btn-success">
                <i class="bi bi-briefcase-fill"></i>
                Jobs
            </a>

            <a href="reports.php" class="btn btn-warning text-dark">
                <i class="bi bi-bar-chart-fill"></i>
                Reports
            </a>

            <a href="settings.php" class="btn btn-dark">
                <i class="bi bi-gear-fill"></i>
                Settings
            </a>

        </div>

    </div>


    <!-- ==========================================
         Dashboard Cards
    =========================================== -->

    <div class="row">

        <?php foreach($cards as $card){ ?>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <small class="text-muted">

                                <?= htmlspecialchars($card['title']); ?>

                            </small>

                            <h2 class="fw-bold mt-2">

                                <?= number_format($card['value']); ?>

                            </h2>

                        </div>

                        <div>

                            <i class="bi bi-<?= $card['icon']; ?>
                               fs-1 text-<?= $card['color']; ?>"></i>

                        </div>

                    </div>

                </div>

                <div class="card-footer bg-transparent border-0">

                    <div class="progress" style="height:6px;">

                        <div
                            class="progress-bar bg-<?= $card['color']; ?>"
                            style="width:100%">

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <?php } ?>

    </div>


    <!-- ==========================================
         Charts
    =========================================== -->

    <div class="row">

        <!-- Monthly Users -->

        <div class="col-lg-8 mb-4">

            <div class="card shadow-sm h-100">

                <div class="card-header bg-white">

                    <h5 class="mb-0">

                        <i class="bi bi-graph-up-arrow text-primary"></i>

                        Monthly User Registrations

                    </h5>

                </div>

                <div class="card-body">

                    <canvas id="userChart"
                            height="120"></canvas>

                </div>

            </div>

        </div>


        <!-- Application Status -->

        <div class="col-lg-4 mb-4">

            <div class="card shadow-sm h-100">

                <div class="card-header bg-white">

                    <h5 class="mb-0">

                        <i class="bi bi-pie-chart-fill text-success"></i>

                        Application Status

                    </h5>

                </div>

                <div class="card-body">

                    <canvas id="statusChart"></canvas>

                </div>

            </div>

        </div>

    </div>


    <!-- ==========================================
         Jobs by Category
    =========================================== -->

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">

            <h5 class="mb-0">

                <i class="bi bi-bar-chart-fill text-warning"></i>

                Jobs by Category

            </h5>

        </div>

        <div class="card-body">

            <canvas id="categoryChart"
                    height="100"></canvas>

        </div>

    </div>

<!-- ==========================================
     Recent Users
========================================== -->

<div class="row">

    <div class="col-lg-6 mb-4">

        <div class="card shadow-sm h-100">

            <div class="card-header bg-white">

                <h5 class="mb-0">

                    <i class="bi bi-people-fill text-primary"></i>

                    Recent Users

                </h5>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="table-light">

                        <tr>

                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>

                        </tr>

                        </thead>

                        <tbody>

                        <?php if($recentUsers->num_rows > 0){ ?>

                            <?php while($user = $recentUsers->fetch_assoc()){ ?>

                            <tr>

                                <td>

                                    <?= htmlspecialchars($user['full_name']); ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($user['email']); ?>

                                </td>

                                <td>

                                    <span class="badge bg-primary">

                                        <?= ucfirst(htmlspecialchars($user['role'])); ?>

                                    </span>

                                </td>

                                <td>

                                    <?= date("d M Y", strtotime($user['created_at'])); ?>

                                </td>

                            </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>

                                <td colspan="4" class="text-center py-4">

                                    No users found.

                                </td>

                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>



    <!-- ==========================================
         Latest Jobs
    =========================================== -->

    <div class="col-lg-6 mb-4">

        <div class="card shadow-sm h-100">

            <div class="card-header bg-white">

                <h5 class="mb-0">

                    <i class="bi bi-briefcase-fill text-success"></i>

                    Latest Jobs

                </h5>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="table-light">

                        <tr>

                            <th>Job</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Posted</th>

                        </tr>

                        </thead>

                        <tbody>

                        <?php if($recentJobs->num_rows > 0){ ?>

                            <?php while($job = $recentJobs->fetch_assoc()){ ?>

                            <tr>

                                <td>

                                    <?= htmlspecialchars($job['title']); ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($job['category']); ?>

                                </td>

                                <td>

                                    <span class="badge bg-<?= $job['status']=="Open" ? "success" : "secondary"; ?>">

                                        <?= htmlspecialchars($job['status']); ?>

                                    </span>

                                </td>

                                <td>

                                    <?= date("d M Y", strtotime($job['created_at'])); ?>

                                </td>

                            </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>

                                <td colspan="4" class="text-center py-4">

                                    No jobs found.

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



<!-- ==========================================
     Recent Applications
========================================== -->

<div class="row">

    <div class="col-lg-6 mb-4">

        <div class="card shadow-sm h-100">

            <div class="card-header bg-white">

                <h5 class="mb-0">

                    <i class="bi bi-file-earmark-text text-primary"></i>

                    Recent Applications

                </h5>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="table-light">

                        <tr>

                            <th>Applicant</th>
                            <th>Job</th>
                            <th>Status</th>
                            <th>Date</th>

                        </tr>

                        </thead>

                        <tbody>

                        <?php if($recentApplications->num_rows > 0){ ?>

                            <?php

                            $statusColors = [

                                "Applied"=>"secondary",
                                "Under Review"=>"warning",
                                "Shortlisted"=>"info",
                                "Interview Scheduled"=>"primary",
                                "Hired"=>"success",
                                "Rejected"=>"danger"

                            ];

                            ?>

                            <?php while($app = $recentApplications->fetch_assoc()){ ?>

                            <tr>

                                <td>

                                    <?= htmlspecialchars($app['full_name']); ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($app['title']); ?>

                                </td>

                                <td>

                                    <span class="badge bg-<?= $statusColors[$app['status']] ?? "dark"; ?>">

                                        <?= htmlspecialchars($app['status']); ?>

                                    </span>

                                </td>

                                <td>

                                    <?= date("d M Y", strtotime($app['applied_at'])); ?>

                                </td>

                            </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>

                                <td colspan="4" class="text-center py-4">

                                    No applications found.

                                </td>

                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>



    <!-- ==========================================
         Upcoming Interviews
    =========================================== -->

    <div class="col-lg-6 mb-4">

        <div class="card shadow-sm h-100">

            <div class="card-header bg-white">

                <h5 class="mb-0">

                    <i class="bi bi-calendar-check text-danger"></i>

                    Upcoming Interviews

                </h5>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="table-light">

                        <tr>

                            <th>Applicant</th>
                            <th>Job</th>
                            <th>Date</th>
                            <th>Type</th>

                        </tr>

                        </thead>

                        <tbody>

                        <?php if($recentInterviews->num_rows > 0){ ?>

                            <?php while($interview = $recentInterviews->fetch_assoc()){ ?>

                            <tr>

                                <td>

                                    <?= htmlspecialchars($interview['full_name']); ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($interview['title']); ?>

                                </td>

                                <td>

                                    <?= date("d M Y", strtotime($interview['interview_date'])); ?>

                                    <br>

                                    <small class="text-muted">

                                        <?= date("g:i A", strtotime($interview['interview_time'])); ?>

                                    </small>

                                </td>

                                <td>

                                    <span class="badge bg-info">

                                        <?= htmlspecialchars($interview['interview_type']); ?>

                                    </span>

                                </td>

                            </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>

                                <td colspan="4" class="text-center py-4">

                                    No upcoming interviews.

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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

Chart.defaults.font.family = "Segoe UI";
Chart.defaults.color = "#6c757d";
Chart.defaults.plugins.legend.position = "bottom";


/* ==========================================
   Monthly User Registrations
========================================== */

new Chart(document.getElementById("userChart"), {

    type: "line",

    data: {

        labels: <?= json_encode($userMonths); ?>,

        datasets: [{

            label: "Registered Users",

            data: <?= json_encode($userTotals); ?>,

            borderColor: "#0d6efd",

            backgroundColor: "rgba(13,110,253,.15)",

            fill: true,

            tension: .35,

            borderWidth: 3,

            pointRadius: 4,

            pointHoverRadius: 6

        }]

    },

    options: {

        responsive: true,

        maintainAspectRatio: false,

        plugins: {

            legend: {

                display: true

            }

        },

        scales: {

            y: {

                beginAtZero: true,

                ticks: {

                    precision: 0

                }

            }

        }

    }

});


/* ==========================================
   Application Status
========================================== */

new Chart(document.getElementById("statusChart"), {

    type: "doughnut",

    data: {

        labels: <?= json_encode($statusLabels); ?>,

        datasets: [{

            data: <?= json_encode($statusTotals); ?>,

            backgroundColor: [

                "#6c757d",
                "#ffc107",
                "#0dcaf0",
                "#0d6efd",
                "#198754",
                "#dc3545"

            ],

            borderWidth: 1

        }]

    },

    options: {

        responsive: true,

        maintainAspectRatio: true,

        plugins: {

            legend: {

                position: "bottom"

            }

        }

    }

});


/* ==========================================
   Jobs by Category
========================================== */

new Chart(document.getElementById("categoryChart"), {

    type: "bar",

    data: {

        labels: <?= json_encode($categoryLabels); ?>,

        datasets: [{

            label: "Jobs",

            data: <?= json_encode($categoryTotals); ?>,

            backgroundColor: "#198754",

            borderRadius: 6

        }]

    },

    options: {

        responsive: true,

        maintainAspectRatio: false,

        scales: {

            y: {

                beginAtZero: true,

                ticks: {

                    precision: 0

                }

            }

        }

    }

});


/* ==========================================
   Auto-hide Alerts
========================================== */

setTimeout(function(){

    document.querySelectorAll(".alert").forEach(function(alert){

        alert.classList.add("fade");

        setTimeout(function(){

            alert.remove();

        },500);

    });

},5000);

</script>

<?php include "includes/footer.php"; ?>

</body>
</html>

