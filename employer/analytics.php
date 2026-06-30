<?php

require_once "../includes/employer_auth.php";
require_once "../config/config.php";

$employer_id = $_SESSION['user_id'];

/* ============================================================
   TOTAL JOBS
============================================================ */

$stmt = $conn->prepare("
SELECT COUNT(*)
FROM jobs
WHERE employer_id = ?
");

$stmt->bind_param("i", $employer_id);
$stmt->execute();
$stmt->bind_result($totalJobs);
$stmt->fetch();
$stmt->close();

/* ============================================================
   OPEN JOBS
============================================================ */

$stmt = $conn->prepare("
SELECT COUNT(*)
FROM jobs
WHERE employer_id = ?
AND status='Open'
");

$stmt->bind_param("i", $employer_id);
$stmt->execute();
$stmt->bind_result($openJobs);
$stmt->fetch();
$stmt->close();

/* ============================================================
   CLOSED JOBS
============================================================ */

$stmt = $conn->prepare("
SELECT COUNT(*)
FROM jobs
WHERE employer_id = ?
AND status='Closed'
");

$stmt->bind_param("i", $employer_id);
$stmt->execute();
$stmt->bind_result($closedJobs);
$stmt->fetch();
$stmt->close();

/* ============================================================
   TOTAL APPLICATIONS
============================================================ */

$stmt = $conn->prepare("
SELECT COUNT(*)
FROM applications a
INNER JOIN jobs j
ON a.job_id=j.job_id
WHERE j.employer_id=?
");

$stmt->bind_param("i",$employer_id);
$stmt->execute();
$stmt->bind_result($totalApplications);
$stmt->fetch();
$stmt->close();

/* ============================================================
   SHORTLISTED
============================================================ */

$stmt=$conn->prepare("
SELECT COUNT(*)
FROM applications a
INNER JOIN jobs j
ON a.job_id=j.job_id
WHERE
j.employer_id=?
AND a.status='Shortlisted'
");

$stmt->bind_param("i",$employer_id);
$stmt->execute();
$stmt->bind_result($shortlisted);
$stmt->fetch();
$stmt->close();

/* ============================================================
   INTERVIEW SCHEDULED
============================================================ */

$stmt=$conn->prepare("
SELECT COUNT(*)
FROM applications a
INNER JOIN jobs j
ON a.job_id=j.job_id
WHERE
j.employer_id=?
AND a.status='Interview Scheduled'
");

$stmt->bind_param("i",$employer_id);
$stmt->execute();
$stmt->bind_result($scheduled);
$stmt->fetch();
$stmt->close();

/* ============================================================
   HIRED
============================================================ */

$stmt=$conn->prepare("
SELECT COUNT(*)
FROM applications a
INNER JOIN jobs j
ON a.job_id=j.job_id
WHERE
j.employer_id=?
AND a.status='Hired'
");

$stmt->bind_param("i",$employer_id);
$stmt->execute();
$stmt->bind_result($hired);
$stmt->fetch();
$stmt->close();

/* ============================================================
   REJECTED
============================================================ */

$stmt=$conn->prepare("
SELECT COUNT(*)
FROM applications a
INNER JOIN jobs j
ON a.job_id=j.job_id
WHERE
j.employer_id=?
AND a.status='Rejected'
");

$stmt->bind_param("i",$employer_id);
$stmt->execute();
$stmt->bind_result($rejected);
$stmt->fetch();
$stmt->close();

/* ============================================================
   TOTAL INTERVIEWS
============================================================ */

$stmt=$conn->prepare("
SELECT COUNT(*)
FROM interviews
WHERE employer_id=?
");

$stmt->bind_param("i",$employer_id);
$stmt->execute();
$stmt->bind_result($totalInterviews);
$stmt->fetch();
$stmt->close();

/* ============================================================
   MONTHLY APPLICATIONS
============================================================ */

$monthLabels = [];
$monthTotals = [];

$stmt = $conn->prepare("
SELECT
    DATE_FORMAT(a.applied_at,'%b %Y') AS month_name,
    COUNT(*) AS total
FROM applications a
INNER JOIN jobs j
ON a.job_id=j.job_id
WHERE j.employer_id=?
GROUP BY YEAR(a.applied_at),MONTH(a.applied_at)
ORDER BY YEAR(a.applied_at),MONTH(a.applied_at)
");

$stmt->bind_param("i",$employer_id);
$stmt->execute();

$result = $stmt->get_result();

while($row=$result->fetch_assoc()){

    $monthLabels[] = $row['month_name'];
    $monthTotals[] = (int)$row['total'];

}

$stmt->close();


/* ============================================================
   JOBS BY CATEGORY
============================================================ */

$categoryLabels = [];
$categoryTotals = [];

$stmt = $conn->prepare("
SELECT
    category,
    COUNT(*) total
FROM jobs
WHERE employer_id=?
GROUP BY category
ORDER BY total DESC
");

$stmt->bind_param("i",$employer_id);
$stmt->execute();

$result = $stmt->get_result();

while($row=$result->fetch_assoc()){

    $categoryLabels[] = $row['category'] ?: "Uncategorized";
    $categoryTotals[] = (int)$row['total'];

}

$stmt->close();

/* ============================================================
   LATEST APPLICATIONS
============================================================ */

$stmt = $conn->prepare("
SELECT
    u.full_name,
    j.title,
    a.status,
    a.applied_at
FROM applications a
INNER JOIN users u
    ON a.applicant_id = u.user_id
INNER JOIN jobs j
    ON a.job_id = j.job_id
WHERE j.employer_id = ?
ORDER BY a.applied_at DESC
LIMIT 8
");

$stmt->bind_param("i",$employer_id);
$stmt->execute();

$latestApplications = $stmt->get_result();

$stmt->close();


/* ============================================================
   UPCOMING INTERVIEWS
============================================================ */

$stmt = $conn->prepare("
SELECT
    u.full_name,
    j.title,
    i.interview_date,
    i.interview_time,
    i.interview_type
FROM interviews i
INNER JOIN applications a
    ON i.application_id = a.application_id
INNER JOIN users u
    ON a.applicant_id = u.user_id
INNER JOIN jobs j
    ON a.job_id = j.job_id
WHERE
    i.employer_id = ?
    AND i.interview_date >= CURDATE()
ORDER BY
    i.interview_date,
    i.interview_time
LIMIT 5
");

$stmt->bind_param("i",$employer_id);
$stmt->execute();

$upcomingInterviews = $stmt->get_result();

$stmt->close();


/* ============================================================
   TOP PERFORMING JOBS
============================================================ */

$stmt = $conn->prepare("
SELECT

    j.title,

    COUNT(a.application_id) total

FROM jobs j

LEFT JOIN applications a

ON j.job_id=a.job_id

WHERE j.employer_id=?

GROUP BY j.job_id

ORDER BY total DESC

LIMIT 5
");

$stmt->bind_param("i",$employer_id);
$stmt->execute();

$topJobs=$stmt->get_result();

$stmt->close();

?>
<?php

/* ============================================================
   APPLICATIONS PER JOB
============================================================ */

$jobLabels = [];
$jobCounts = [];

$stmt = $conn->prepare("
SELECT
    j.title,
    COUNT(a.application_id) AS total
FROM jobs j
LEFT JOIN applications a
ON j.job_id = a.job_id
WHERE j.employer_id = ?
GROUP BY j.job_id
ORDER BY total DESC
");

$stmt->bind_param("i", $employer_id);
$stmt->execute();

$result = $stmt->get_result();

while($row = $result->fetch_assoc()){

    $jobLabels[] = $row['title'];
    $jobCounts[] = (int)$row['total'];

}

$stmt->close();

/* ============================================================
   APPLICATION STATUS DISTRIBUTION
============================================================ */

$statusLabels = [];
$statusCounts = [];

$stmt = $conn->prepare("
SELECT
    a.status,
    COUNT(*) AS total
FROM applications a
INNER JOIN jobs j
ON a.job_id=j.job_id
WHERE j.employer_id=?
GROUP BY a.status
");

$stmt->bind_param("i",$employer_id);
$stmt->execute();

$result = $stmt->get_result();

while($row=$result->fetch_assoc()){

    $statusLabels[] = $row['status'];
    $statusCounts[] = (int)$row['total'];

}

$stmt->close();
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Employer Analytics</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>

body{

background:#f5f7fb;

}

.card{

border:none;
border-radius:15px;

}

.stat-card{

transition:.3s;

}

.stat-card:hover{

transform:translateY(-5px);

}

.stat-icon{

font-size:40px;

opacity:.15;

position:absolute;

right:20px;

top:20px;

}

</style>

</head>

<body>

<div class="container-fluid py-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2>

<i class="bi bi-bar-chart-line-fill text-primary"></i>

Employer Analytics Dashboard

</h2>

<p class="text-muted">

Recruitment Performance Overview

</p>

</div>

<a
href="dashboard.php"
class="btn btn-primary">

<i class="bi bi-speedometer2"></i>

Dashboard

</a>

</div>

<!-- ===========================
     STATISTICS CARDS
=========================== -->

<div class="row g-4">

<div class="col-lg-3 col-md-6">

<div class="card stat-card shadow">

<div class="card-body position-relative">

<i class="bi bi-briefcase-fill stat-icon text-primary"></i>

<h6>Total Jobs</h6>

<h2><?= $totalJobs ?></h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stat-card shadow">

<div class="card-body position-relative">

<i class="bi bi-folder-check stat-icon text-success"></i>

<h6>Open Jobs</h6>

<h2><?= $openJobs ?></h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stat-card shadow">

<div class="card-body position-relative">

<i class="bi bi-folder-x stat-icon text-danger"></i>

<h6>Closed Jobs</h6>

<h2><?= $closedJobs ?></h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stat-card shadow">

<div class="card-body position-relative">

<i class="bi bi-people-fill stat-icon text-info"></i>

<h6>Total Applicants</h6>

<h2><?= $totalApplications ?></h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stat-card shadow">

<div class="card-body position-relative">

<i class="bi bi-star-fill stat-icon text-warning"></i>

<h6>Shortlisted</h6>

<h2><?= $shortlisted ?></h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stat-card shadow">

<div class="card-body position-relative">

<i class="bi bi-calendar-check-fill stat-icon text-primary"></i>

<h6>Interviews</h6>

<h2><?= $scheduled ?></h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stat-card shadow">

<div class="card-body position-relative">

<i class="bi bi-check-circle-fill stat-icon text-success"></i>

<h6>Hired</h6>

<h2><?= $hired ?></h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stat-card shadow">

<div class="card-body position-relative">

<i class="bi bi-x-circle-fill stat-icon text-danger"></i>

<h6>Rejected</h6>

<h2><?= $rejected ?></h2>

</div>

</div>

</div>

</div>

<!-- ===========================
     CHARTS PLACEHOLDERS
=========================== -->

<div class="row mt-5">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header bg-white">

<h5>

Applications Per Job

</h5>

</div>

<div class="card-body">

<canvas id="applicationsChart"></canvas>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card shadow">

<div class="card-header bg-white">

<h5>

Application Status

</h5>

</div>

<div class="card-body">

<canvas id="statusChart"></canvas>

</div>

</div>

</div>

</div>

<div class="row mt-4">

    <div class="col-lg-8">

        <div class="card shadow">

            <div class="card-header bg-white">

                <h5>

                    Monthly Applications Trend

                </h5>

            </div>

            <div class="card-body">

                <canvas id="monthlyChart"></canvas>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="card shadow">

            <div class="card-header bg-white">

                <h5>

                    Jobs By Category

                </h5>

            </div>

            <div class="card-body">

                <canvas id="categoryChart"></canvas>

            </div>

        </div>

    </div>

</div>

<div class="row mt-4">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header bg-white">

<h5>

Latest Applications

</h5>

</div>

<div class="table-responsive">

<table class="table table-hover align-middle mb-0">

<thead class="table-light">

<tr>

<th>Applicant</th>

<th>Job</th>

<th>Status</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php while($row=$latestApplications->fetch_assoc()){ ?>

<tr>

<td>

<?= htmlspecialchars($row['full_name']) ?>

</td>

<td>

<?= htmlspecialchars($row['title']) ?>

</td>

<td>

<?php

$statusColor="secondary";

switch($row['status']){

case "Applied":
$statusColor="primary";
break;

case "Shortlisted":
$statusColor="warning";
break;

case "Interview Scheduled":
$statusColor="info";
break;

case "Interview Completed":
$statusColor="dark";
break;

case "Hired":
$statusColor="success";
break;

case "Rejected":
$statusColor="danger";
break;

}

?>

<span class="badge bg-<?= $statusColor ?>">

<?= $row['status'] ?>

</span>

</td>

<td>

<?= date("d M Y",strtotime($row['applied_at'])) ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card shadow mb-4">

<div class="card-header bg-white">

<h5>

Upcoming Interviews

</h5>

</div>

<div class="list-group list-group-flush">

<?php while($row=$upcomingInterviews->fetch_assoc()){ ?>

<div class="list-group-item">

<strong>

<?= htmlspecialchars($row['full_name']) ?>

</strong>

<br>

<?= htmlspecialchars($row['title']) ?>

<br>

<small class="text-muted">

<?= date("d M Y",strtotime($row['interview_date'])) ?>

|

<?= date("g:i A",strtotime($row['interview_time'])) ?>

</small>

</div>

<?php } ?>

</div>

</div>

<div class="card shadow">

<div class="card-header bg-white">

<h5>

Top Jobs

</h5>

</div>

<div class="card-body">

<?php

$rank=1;

while($row=$topJobs->fetch_assoc()){

?>

<div class="mb-4">

<div class="d-flex justify-content-between">

<strong>

<?= $rank ?>.

<?= htmlspecialchars($row['title']) ?>

</strong>

<span>

<?= $row['total'] ?>

Applicants

</span>

</div>

<div class="progress mt-2">

<div

class="progress-bar"

style="width:<?= min($row['total']*10,100) ?>%">

</div>

</div>

</div>

<?php

$rank++;

}

?>

</div>

</div>

</div>

</div>

<div class="card shadow mt-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

Recruitment Summary

</h5>

</div>

<div class="card-body">

<div class="row text-center">

<div class="col-md-3">

<h3 class="text-primary">

<?= $totalApplications ?>

</h3>

<p>Total Applications</p>

</div>

<div class="col-md-3">

<h3 class="text-warning">

<?= $shortlisted ?>

</h3>

<p>Shortlisted</p>

</div>

<div class="col-md-3">

<h3 class="text-success">

<?= $hired ?>

</h3>

<p>Hired</p>

</div>

<div class="col-md-3">

<h3 class="text-danger">

<?= $rejected ?>

</h3>

<p>Rejected</p>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const monthlyChart =
document.getElementById("monthlyChart");

new Chart(monthlyChart,{

    type:'line',

    data:{

        labels: <?= json_encode($monthLabels); ?>,

        datasets:[{

            label:'Applications',

            data: <?= json_encode($monthTotals); ?>,

            borderColor:'#0d6efd',

            backgroundColor:'rgba(13,110,253,.15)',

            tension:.35,

            fill:true,

            pointRadius:5,

            pointHoverRadius:7

        }]

    },

    options:{

        responsive:true,

        plugins:{

            legend:{

                display:false

            }

        },

        scales:{

            y:{

                beginAtZero:true,

                ticks:{

                    precision:0

                }

            }

        }

    }

});

</script>

<script>

const categoryChart =
document.getElementById("categoryChart");

new Chart(categoryChart,{

    type:'doughnut',

    data:{

        labels: <?= json_encode($categoryLabels); ?>,

        datasets:[{

            data: <?= json_encode($categoryTotals); ?>,

            backgroundColor:[

                '#0d6efd',

                '#20c997',

                '#ffc107',

                '#dc3545',

                '#6610f2',

                '#fd7e14',

                '#198754',

                '#6f42c1',

                '#0dcaf0',

                '#adb5bd'

            ],

            hoverOffset:12

        }]

    },

    options:{

        responsive:true,

        plugins:{

            legend:{

                position:'bottom'

            }

        }

    }

});

</script>

<script>

const jobChart = document.getElementById("applicationsChart");

new Chart(jobChart,{

    type:'bar',

    data:{

        labels: <?= json_encode($jobLabels); ?>,

        datasets:[{

            label:'Applications',

            data: <?= json_encode($jobCounts); ?>,

            backgroundColor:[
                '#0d6efd',
                '#20c997',
                '#ffc107',
                '#dc3545',
                '#6f42c1',
                '#198754',
                '#fd7e14',
                '#6610f2'
            ],

            borderRadius:8,

            borderWidth:1

        }]

    },

    options:{

        responsive:true,

        plugins:{

            legend:{
                display:false
            }

        },

        scales:{

            y:{
                beginAtZero:true
            }

        }

    }

});

</script>

<script>

const statusChart=document.getElementById("statusChart");

new Chart(statusChart,{

    type:'pie',

    data:{

        labels: <?= json_encode($statusLabels); ?>,

        datasets:[{

            data: <?= json_encode($statusCounts); ?>,

            backgroundColor:[

                '#0d6efd',

                '#ffc107',

                '#20c997',

                '#6610f2',

                '#198754',

                '#dc3545',

                '#6c757d'

            ]

        }]

    },

    options:{

        responsive:true,

        plugins:{

            legend:{

                position:'bottom'

            }

        }

    }

});

</script>

</div>

</body>

</html>