<?php
session_start();

require_once "../includes/employer_auth.php";
require_once "../config/config.php";
$page_css = "employer\css\manage_job.css";

/*==================================================
SEARCH & FILTERS
==================================================*/

$search = trim($_GET['search'] ?? "");
$status = trim($_GET['status'] ?? "");
$category = trim($_GET['category'] ?? "");
$employment_type = trim($_GET['employment_type'] ?? "");
$sort = $_GET['sort'] ?? "newest";

$page = isset($_GET['page']) && is_numeric($_GET['page'])
    ? max(1, (int)$_GET['page'])
    : 1;

$limit = isset($_GET['limit']) && is_numeric($_GET['limit'])
    ? (int)$_GET['limit']
    : 10;

$allowedLimits = [10,25,50,100];

if(!in_array($limit,$allowedLimits)){
    $limit = 10;
}

$offset = ($page-1) * $limit;

/*==================================================
WHERE CLAUSE
==================================================*/

$where = " WHERE 1=1 ";

$params = [];
$types = "";

/*==================================================
SEARCH
==================================================*/

if($search != ""){

    $where .= "

    AND (

        jobs.title LIKE ?

        OR jobs.location LIKE ?

        OR users.full_name LIKE ?

    )

    ";

    $term = "%{$search}%";

    $params[] = $term;
    $params[] = $term;
    $params[] = $term;

    $types .= "sss";

}

/*==================================================
STATUS
==================================================*/

if($status != ""){

    $where .= " AND jobs.status=? ";

    $params[] = $status;

    $types .= "s";

}

/*==================================================
CATEGORY
==================================================*/

if($category != ""){

    $where .= " AND jobs.category=? ";

    $params[] = $category;

    $types .= "s";

}

/*==================================================
EMPLOYMENT TYPE
==================================================*/

if($employment_type != ""){

    $where .= " AND jobs.employment_type=? ";

    $params[] = $employment_type;

    $types .= "s";

}

/*==================================================
SORTING
==================================================*/

switch($sort){

    case "oldest":
        $orderBy="ORDER BY jobs.created_at ASC";
        break;

    case "deadline":
        $orderBy="ORDER BY jobs.deadline ASC";
        break;

    case "title":
        $orderBy="ORDER BY jobs.title ASC";
        break;

    case "applications":
        $orderBy="ORDER BY applicants DESC";
        break;

    default:
        $orderBy="ORDER BY jobs.created_at DESC";

}

/*==================================================
TOTAL JOBS
==================================================*/

$countSQL="

SELECT COUNT(*)

FROM jobs

INNER JOIN users

ON users.user_id=jobs.employer_id

{$where}

";

$stmt=$conn->prepare($countSQL);

if(!empty($params)){

    $stmt->bind_param($types,...$params);

}

$stmt->execute();

$totalJobs=$stmt->get_result()->fetch_row()[0];

$totalPages=max(1,ceil($totalJobs/$limit));

/*==================================================
LOAD JOBS
==================================================*/

$sql="

SELECT

jobs.*,

users.full_name AS employer,

COUNT(applications.application_id) AS applicants

FROM jobs

INNER JOIN users

ON users.user_id=jobs.employer_id

LEFT JOIN applications

ON applications.job_id=jobs.job_id

{$where}

GROUP BY jobs.job_id

{$orderBy}

LIMIT ?,?

";

$params2=$params;

$params2[]=$offset;
$params2[]=$limit;

$types2=$types."ii";

$stmt=$conn->prepare($sql);

$stmt->bind_param($types2,...$params2);

$stmt->execute();

$jobs=$stmt->get_result();

/*==================================================
STATISTICS
==================================================*/

$stats=$conn->query("

SELECT

COUNT(*) total,

SUM(status='Open') open_jobs,

SUM(status='Closed') closed_jobs,

SUM(status='Expired') expired_jobs,

SUM(status='Draft') drafts

FROM jobs

")->fetch_assoc();

/*==================================================
CATEGORY LIST
==================================================*/

$categories=$conn->query("

SELECT DISTINCT category

FROM jobs

ORDER BY category

");

/*==================================================
EMPLOYMENT TYPES
==================================================*/

$employmentTypes=[

"Full-Time",

"Part-Time",

"Contract",

"Internship",

"Remote"

];

/*==================================================
HELPER FUNCTIONS
==================================================*/

function statusBadge($status){

switch(strtolower($status)){

case "open":
return "success";

case "closed":
return "danger";

case "expired":
return "secondary";

case "draft":
return "warning";

default:
return "primary";

}

}

function deadlineBadge($deadline){

if(empty($deadline)){

return "secondary";

}

$today=strtotime(date("Y-m-d"));

$date=strtotime($deadline);

if($date<$today){

return "danger";

}

if(($date-$today)<=604800){

return "warning";

}

return "success";

}

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
href="employer\css\manage_job.css">

</head>

<body>

<div class="container-fluid">

<!-- ==========================================
PAGE HEADER
========================================== -->

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold">

            <i class="bi bi-briefcase-fill text-primary"></i>

            Manage Jobs

        </h2>

        <p class="text-muted mb-0">

            View, manage and monitor all job postings.

        </p>

    </div>

    <div>

        <a href="dashboard.php" class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>

            Dashboard

        </a>

        <a href="../employer/post_job.php" class="btn btn-primary">

            <i class="bi bi-plus-circle"></i>

            Add Job

        </a>

    </div>

</div>

<!-- ==========================================
ALERTS
========================================== -->

<?php if(isset($_SESSION['success'])){ ?>

<div class="alert alert-success alert-dismissible fade show">

<i class="bi bi-check-circle-fill"></i>

<?= htmlspecialchars($_SESSION['success']) ?>

<button
type="button"
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php unset($_SESSION['success']); } ?>

<?php if(isset($_SESSION['error'])){ ?>

<div class="alert alert-danger alert-dismissible fade show">

<i class="bi bi-exclamation-circle-fill"></i>

<?= htmlspecialchars($_SESSION['error']) ?>

<button
type="button"
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php unset($_SESSION['error']); } ?>

<!-- ==========================================
STATISTICS
========================================== -->

<div class="row mb-4">

<div class="col-lg-3 col-md-6 mb-3">

<div class="card shadow-sm border-0">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6 class="text-muted">

Total Jobs

</h6>

<h3>

<?= $stats['total'] ?? 0 ?>

</h3>

</div>

<i class="bi bi-briefcase-fill fs-1 text-primary"></i>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-3">

<div class="card shadow-sm border-start border-success border-4">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6 class="text-muted">

Open Jobs

</h6>

<h3 class="text-success">

<?= $stats['open_jobs'] ?? 0 ?>

</h3>

</div>

<i class="bi bi-check-circle-fill fs-1 text-success"></i>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-3">

<div class="card shadow-sm border-start border-danger border-4">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6 class="text-muted">

Closed Jobs

</h6>

<h3 class="text-danger">

<?= $stats['closed_jobs'] ?? 0 ?>

</h3>

</div>

<i class="bi bi-lock-fill fs-1 text-danger"></i>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-3">

<div class="card shadow-sm border-start border-warning border-4">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6 class="text-muted">

Expired Jobs

</h6>

<h3 class="text-warning">

<?= $stats['expired_jobs'] ?? 0 ?>

</h3>

</div>

<i class="bi bi-clock-history fs-1 text-warning"></i>

</div>

</div>

</div>

</div>

</div>

<!-- ==========================================
SEARCH & FILTERS
========================================== -->

<div class="card shadow-sm border-0 mb-4">

<div class="card-header bg-light">

<h5 class="mb-0">

<i class="bi bi-funnel-fill"></i>

Search & Filters

</h5>

</div>

<div class="card-body">

<form method="GET">

<div class="row g-3">

<div class="col-lg-3">

<label class="form-label">

Search

</label>

<input
type="text"
name="search"
class="form-control"
placeholder="Title, employer or location"
value="<?= htmlspecialchars($search) ?>">

</div>

<div class="col-lg-2">

<label class="form-label">

Status

</label>

<select
name="status"
class="form-select">

<option value="">All</option>

<option value="Open" <?= $status=="Open"?"selected":"" ?>>Open</option>

<option value="Closed" <?= $status=="Closed"?"selected":"" ?>>Closed</option>

<option value="Expired" <?= $status=="Expired"?"selected":"" ?>>Expired</option>

<option value="Draft" <?= $status=="Draft"?"selected":"" ?>>Draft</option>

</select>

</div>

<div class="col-lg-2">

<label class="form-label">

Category

</label>

<select
name="category"
class="form-select">

<option value="">All Categories</option>

<?php while($cat=$categories->fetch_assoc()){ ?>

<option
value="<?= htmlspecialchars($cat['category']) ?>"
<?= $category==$cat['category']?"selected":"" ?>>

<?= htmlspecialchars($cat['category']) ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-lg-2">

<label class="form-label">

Employment

</label>

<select
name="employment_type"
class="form-select">

<option value="">All</option>

<?php foreach($employmentTypes as $type){ ?>

<option
value="<?= $type ?>"
<?= $employment_type==$type?"selected":"" ?>>

<?= $type ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-lg-2">

<label class="form-label">

Sort

</label>

<select
name="sort"
class="form-select">

<option value="newest" <?= $sort=="newest"?"selected":"" ?>>Newest</option>

<option value="oldest" <?= $sort=="oldest"?"selected":"" ?>>Oldest</option>

<option value="deadline" <?= $sort=="deadline"?"selected":"" ?>>Deadline</option>

<option value="title" <?= $sort=="title"?"selected":"" ?>>Title</option>

<option value="applications" <?= $sort=="applications"?"selected":"" ?>>Applications</option>

</select>

</div>

<div class="col-lg-1">

<label class="form-label">

Show

</label>

<select
name="limit"
class="form-select">

<option value="10" <?= $limit==10?"selected":"" ?>>10</option>
<option value="25" <?= $limit==25?"selected":"" ?>>25</option>
<option value="50" <?= $limit==50?"selected":"" ?>>50</option>
<option value="100" <?= $limit==100?"selected":"" ?>>100</option>

</select>

</div>

</div>

<div class="mt-4">

<button
type="submit"
class="btn btn-primary">

<i class="bi bi-search"></i>

Search

</button>

<a
href="manage_job.php"
class="btn btn-outline-secondary">

<i class="bi bi-arrow-clockwise"></i>

Reset

</a>

</div>

</form>

</div>

</div>

<!-- ==========================================
JOBS TABLE
========================================== -->

<div class="card shadow-sm border-0">

    <div class="card-header bg-primary text-white">

        <div class="d-flex justify-content-between align-items-center">

            <h5 class="mb-0">

                <i class="bi bi-list-ul"></i>

                Job Listings

            </h5>

            <span class="badge bg-light text-dark">

                <?= $totalJobs ?> Jobs Found

            </span>

        </div>

    </div>

    <div class="card-body p-0">

    <?php if($jobs->num_rows > 0){ ?>

    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

            <thead class="table-light">

                <tr>

                    <th width="50">
                    <input
                            type="checkbox"
                            id="selectAllJobs"
                            class="form-check-input">
                    </th>

                    <th width="70">Logo</th>

                    <th>Job Details</th>

                    <th>Employer</th>

                    <th>Category</th>

                    <th>Employment</th>

                    <th>Location</th>

                    <th>Salary</th>

                    <th class="text-center">Applicants</th>

                    <th>Status</th>

                    <th>Deadline</th>

                    <th>Posted</th>

                    <th class="text-center">Actions</th>

                </tr>

            </thead>

            <tbody>

            <?php while($job = $jobs->fetch_assoc()){ ?>

            <?php

            $logo = "../assets/images/company.png";

            if(
                !empty($job['company_logo']) &&
                file_exists("../uploads/company_logos/".$job['company_logo'])
            ){
                $logo = "../uploads/company_logos/".$job['company_logo'];
            }

            ?>

            <tr>

                <td>

                    <img
                    src="<?= $logo ?>"
                    width="55"
                    height="55"
                    class="rounded border"
                    style="object-fit:cover;">

                </td>

                <td>

                    <strong>

                        <?= htmlspecialchars($job['title']) ?>

                    </strong>

                    <br>

                    <small class="text-muted">

                        Job ID #<?= $job['job_id'] ?>

                    </small>

                </td>

                <td>

                    <?= htmlspecialchars($job['employer']) ?>

                </td>

                <td>

                    <span class="badge bg-info">

                        <?= htmlspecialchars($job['category']) ?>

                    </span>

                </td>

                <td>

                    <span class="badge bg-secondary">

                        <?= htmlspecialchars($job['employment_type']) ?>

                    </span>

                </td>

                <td>

                    <i class="bi bi-geo-alt"></i>

                    <?= htmlspecialchars($job['location']) ?>

                </td>

                <td>

                    <?php

                    if(!empty($job['salary'])){

                        echo htmlspecialchars($job['salary']);

                    }else{

                        echo "<span class='text-muted'>Negotiable</span>";

                    }

                    ?>

                </td>

                <td class="text-center">

                    <span class="badge bg-dark">

                        <?= $job['applicants'] ?>

                    </span>

                </td>

                <td>

                    <span class="badge bg-<?= statusBadge($job['status']) ?>">

                        <?= htmlspecialchars($job['status']) ?>

                    </span>

                </td>

                <td>

                    <?php if(!empty($job['deadline'])){ ?>

                        <span class="badge bg-<?= deadlineBadge($job['deadline']) ?>">

                            <?= date("d M Y",strtotime($job['deadline'])) ?>

                        </span>

                    <?php }else{ ?>

                        <span class="text-muted">

                            None

                        </span>

                    <?php } ?>

                </td>

                <td>

                    <?= date("d M Y",strtotime($job['created_at'])) ?>

                    <br>

                    <small class="text-muted">

                        <?= date("g:i A",strtotime($job['created_at'])) ?>

                    </small>

                </td>

                <td class="text-center">

                <div class="dropdown">

    <button
        class="btn btn-outline-primary btn-sm dropdown-toggle"
        type="button"
        data-bs-toggle="dropdown">

        <i class="bi bi-three-dots"></i>

        Actions

    </button>

    <ul class="dropdown-menu dropdown-menu-end">

        <li>

            <a
                class="dropdown-item"
                href="view_job.php?id=<?= $job['job_id'] ?>">

                <i class="bi bi-eye text-primary"></i>

                View Job

            </a>

        </li>

        <li>

            <a
                class="dropdown-item"
                href="edit_job.php?id=<?= $job['job_id'] ?>">

                <i class="bi bi-pencil-square text-warning"></i>

                Edit Job

            </a>

        </li>

        <li>

            <a
                class="dropdown-item"
                href="view_applicants.php?job_id=<?= $job['job_id'] ?>">

                <i class="bi bi-people-fill text-success"></i>

                View Applicants

                <span class="badge bg-success ms-2">

                    <?= $job['applicants'] ?>

                </span>

            </a>

        </li>

        <li><hr class="dropdown-divider"></li>

        <?php if(strtolower($job['status']) == "open"){ ?>

        <li>

            <a
                class="dropdown-item text-warning"
                href="toggle_job_status.php?id=<?= $job['job_id'] ?>&status=Closed">

                <i class="bi bi-lock-fill"></i>

                Close Job

            </a>

        </li>

        <?php }else{ ?>

        <li>

            <a
                class="dropdown-item text-success"
                href="toggle_job_status.php?id=<?= $job['job_id'] ?>&status=Open">

                <i class="bi bi-unlock-fill"></i>

                Reopen Job

            </a>

        </li>

        <?php } ?>

        <li><hr class="dropdown-divider"></li>

        <li>

            <a
                class="dropdown-item text-danger"
                href="delete_job.php?id=<?= $job['job_id'] ?>"
                onclick="return confirm('Delete this job permanently?');">

                <i class="bi bi-trash-fill"></i>

                Delete Job

            </a>

        </li>

    </ul>

</div>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<?php } ?>

</div>

</div>

<!-- ==========================================
PAGINATION
========================================== -->

<div class="row align-items-center mt-4">

    <div class="col-md-4">

        <p class="text-muted mb-0">

            Showing

            <strong>

                <?= $offset + 1 ?>

            </strong>

            -

            <strong>

                <?= min($offset + $limit, $totalJobs) ?>

            </strong>

            of

            <strong>

                <?= $totalJobs ?>

            </strong>

            jobs

        </p>

    </div>

    <div class="col-md-8">

        <nav>

            <ul class="pagination justify-content-end mb-0">

                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">

                    <a
                        class="page-link"
                        href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&category=<?= urlencode($category) ?>&employment_type=<?= urlencode($employment_type) ?>&sort=<?= urlencode($sort) ?>&limit=<?= $limit ?>">

                        <i class="bi bi-chevron-left"></i>

                    </a>

                </li>

                <?php

                $start = max(1, $page - 2);
                $end   = min($totalPages, $page + 2);

                for($i = $start; $i <= $end; $i++):

                ?>

                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">

                    <a
                        class="page-link"
                        href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&category=<?= urlencode($category) ?>&employment_type=<?= urlencode($employment_type) ?>&sort=<?= urlencode($sort) ?>&limit=<?= $limit ?>">

                        <?= $i ?>

                    </a>

                </li>

                <?php endfor; ?>

                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">

                    <a
                        class="page-link"
                        href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&category=<?= urlencode($category) ?>&employment_type=<?= urlencode($employment_type) ?>&sort=<?= urlencode($sort) ?>&limit=<?= $limit ?>">

                        <i class="bi bi-chevron-right"></i>

                    </a>

                </li>

            </ul>

        </nav>

    </div>

</div>

<!-- ==========================================
BULK ACTIONS
========================================== -->

<div class="card shadow-sm border-0 mt-4">

    <div class="card-header bg-light">

        <div class="d-flex justify-content-between align-items-center flex-wrap">

            <div>

                <h5 class="mb-0">

                    <i class="bi bi-list-check"></i>

                    Bulk Actions

                </h5>

            </div>

            <div>

                <button
                    type="button"
                    class="btn btn-success btn-sm"
                    id="bulkOpen">

                    <i class="bi bi-unlock-fill"></i>

                    Open Selected

                </button>

                <button
                    type="button"
                    class="btn btn-warning btn-sm"
                    id="bulkClose">

                    <i class="bi bi-lock-fill"></i>

                    Close Selected

                </button>

                <button
                    type="button"
                    class="btn btn-danger btn-sm"
                    id="bulkDelete">

                    <i class="bi bi-trash-fill"></i>

                    Delete Selected

                </button>

                <a
                    href="export_jobs.php"
                    class="btn btn-primary btn-sm">

                    <i class="bi bi-download"></i>

                    Export CSV

                </a>

                <button
                    class="btn btn-secondary btn-sm"
                    onclick="window.print();">

                    <i class="bi bi-printer-fill"></i>

                    Print

                </button>

            </div>

        </div>

    </div>

    <div class="card-body">

        <div class="form-check">

            <input
                class="form-check-input"
                type="checkbox"
                id="selectAllJobs">

            <label
                class="form-check-label"
                for="selectAllJobs">

                Select / Deselect All Jobs

            </label>

        </div>

        <small class="text-muted">

            Selected jobs can be opened, closed or deleted simultaneously.

        </small>

    </div>

</div>

<!-- ==========================================
CONFIRMATION MODAL
========================================== -->

<div
class="modal fade"
id="bulkActionModal"
tabindex="-1">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header bg-danger text-white">

<h5 class="modal-title">

<i class="bi bi-exclamation-triangle-fill"></i>

Confirm Bulk Action

</h5>

<button
type="button"
class="btn-close btn-close-white"
data-bs-dismiss="modal">

</button>

</div>

<div class="modal-body">

<p id="bulkActionMessage">

</p>

<div class="alert alert-warning mb-0">

This operation may affect multiple job postings.

</div>

</div>

<div class="modal-footer">

<button
class="btn btn-secondary"
data-bs-dismiss="modal">

Cancel

</button>

<form
method="POST"
action="bulk_jobs.php">

<input
type="hidden"
name="action"
id="bulkActionType">

<input
type="hidden"
name="selected_jobs"
id="selectedJobsInput">

<button
type="submit"
class="btn btn-danger">

Proceed

</button>

</form>

</div>

</div>

</div>

</div>

<!-- ==========================================
LOADING SPINNER
========================================== -->

<div
id="loadingOverlay"
style="
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(255,255,255,.8);
z-index:9999;
">

<div
class="d-flex justify-content-center align-items-center h-100">

<div class="text-center">

<div
class="spinner-border text-primary"
style="width:4rem;height:4rem;">

</div>

<h5 class="mt-3">

Processing Jobs...

</h5>

</div>

</div>

</div>

<!-- ==========================================
JAVASCRIPT
========================================== -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    /*=========================================
    BOOTSTRAP TOOLTIPS
    =========================================*/

    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );

    tooltipTriggerList.map(function (tooltipTriggerEl) {

        return new bootstrap.Tooltip(tooltipTriggerEl);

    });

    /*=========================================
    SELECT ALL JOBS
    =========================================*/

    const selectAll = document.getElementById("selectAllJobs");

    if(selectAll){

        selectAll.addEventListener("change", function(){

            document.querySelectorAll(".job-checkbox").forEach(function(box){

                box.checked = selectAll.checked;

            });

        });

    }

    /*=========================================
    BULK ACTIONS
    =========================================*/

    function selectedJobs(){

        let ids=[];

        document.querySelectorAll(".job-checkbox:checked").forEach(function(box){

            ids.push(box.value);

        });

        return ids;

    }

    function showBulkModal(action){

        let ids=selectedJobs();

        if(ids.length===0){

            alert("Please select at least one job.");

            return;

        }

        document.getElementById("bulkActionType").value=action;

        document.getElementById("selectedJobsInput").value=ids.join(",");

        document.getElementById("bulkActionMessage").innerHTML=

            "You are about to <strong>"+action+"</strong> <strong>"+ids.length+"</strong> selected job(s).";

        let modal=new bootstrap.Modal(

            document.getElementById("bulkActionModal")

        );

        modal.show();

    }

    const bulkOpen=document.getElementById("bulkOpen");

    if(bulkOpen){

        bulkOpen.onclick=function(){

            showBulkModal("open");

        };

    }

    const bulkClose=document.getElementById("bulkClose");

    if(bulkClose){

        bulkClose.onclick=function(){

            showBulkModal("close");

        };

    }

    const bulkDelete=document.getElementById("bulkDelete");

    if(bulkDelete){

        bulkDelete.onclick=function(){

            showBulkModal("delete");

        };

    }

    /*=========================================
    SHOW LOADING OVERLAY
    =========================================*/

    document.querySelectorAll("form").forEach(function(form){

        form.addEventListener("submit",function(){

            let overlay=document.getElementById("loadingOverlay");

            if(overlay){

                overlay.style.display="block";

            }

        });

    });

    /*=========================================
    AUTO HIDE ALERTS
    =========================================*/

    setTimeout(function(){

        document.querySelectorAll(".alert").forEach(function(alertBox){

            let alert=new bootstrap.Alert(alertBox);

            alert.close();

        });

    },5000);

});

/*=========================================
PRINT SUPPORT
=========================================*/

window.onbeforeprint=function(){

    document.querySelectorAll(".dropdown").forEach(function(item){

        item.style.display="none";

    });

};

window.onafterprint=function(){

    document.querySelectorAll(".dropdown").forEach(function(item){

        item.style.display="block";

    });

};

</script>

<?php include "includes/footer.php"; ?>