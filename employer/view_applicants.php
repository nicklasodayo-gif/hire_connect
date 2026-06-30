<?php
require_once "../includes/employer_auth.php";
require_once "../config/config.php";
$employer_id = $_SESSION['user_id'];

/* ===========================================================
   FILTERS
=========================================================== */

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$from   = trim($_GET['from'] ?? '');
$to     = trim($_GET['to'] ?? '');

$page = isset($_GET['page']) && $_GET['page'] > 0
    ? (int)$_GET['page']
    : 1;

$limit = 10;
$offset = ($page - 1) * $limit;

/* ===========================================================
   STATUS COLORS
=========================================================== */

$statusColors = [
    "Applied" => "secondary",
    "Under Review" => "warning",
    "Shortlisted" => "info",
    "Interview Scheduled" => "primary",
    "Hired" => "success",
    "Rejected" => "danger"
];

/* ===========================================================
   EMPLOYER DASHBOARD STATISTICS
=========================================================== */

function countApplications($conn, $employer_id, $status = null)
{
    if ($status === null) {

        $stmt = $conn->prepare("
            SELECT COUNT(*) total
            FROM applications a
            JOIN jobs j ON a.job_id=j.job_id
            WHERE j.employer_id=?
        ");

        $stmt->bind_param("i", $employer_id);

    } else {

        $stmt = $conn->prepare("
            SELECT COUNT(*) total
            FROM applications a
            JOIN jobs j ON a.job_id=j.job_id
            WHERE j.employer_id=?
            AND a.status=?
        ");

        $stmt->bind_param("is", $employer_id, $status);
    }

    $stmt->execute();

    return $stmt->get_result()->fetch_assoc()['total'];
}

$total        = countApplications($conn,$employer_id);
$applied      = countApplications($conn,$employer_id,"Applied");
$review       = countApplications($conn,$employer_id,"Under Review");
$shortlisted  = countApplications($conn,$employer_id,"Shortlisted");
$interviews   = countApplications($conn,$employer_id,"Interview Scheduled");
$hired        = countApplications($conn,$employer_id,"Hired");

/* ===========================================================
   BUILD SEARCH QUERY
=========================================================== */

$sql = "
SELECT
    a.application_id,
    a.status,
    a.cv_file,
    a.applied_at,

    u.user_id,
    u.full_name,
    u.email,

    j.job_id,
    j.title

FROM applications a

JOIN users u
ON u.user_id=a.applicant_id

JOIN jobs j
ON j.job_id=a.job_id

WHERE j.employer_id=?
";

$types = "i";
$params = [$employer_id];

if($search != ""){

    $sql .= "
    AND(
        u.full_name LIKE ?
        OR u.email LIKE ?
        OR j.title LIKE ?
    )";

    $like="%{$search}%";

    $types.="sss";

    array_push(
        $params,
        $like,
        $like,
        $like
    );
}

if($status!=""){

    $sql.=" AND a.status=?";

    $types.="s";

    $params[]=$status;
}

if($from!=""){

    $sql.=" AND DATE(a.applied_at)>=?";

    $types.="s";

    $params[]=$from;
}

if($to!=""){

    $sql.=" AND DATE(a.applied_at)<=?";

    $types.="s";

    $params[]=$to;
}

/* ===========================================================
   TOTAL RECORDS
=========================================================== */

$countSql=str_replace(
"SELECT
    a.application_id,
    a.status,
    a.cv_file,
    a.applied_at,

    u.user_id,
    u.full_name,
    u.email,

    j.job_id,
    j.title",
"SELECT COUNT(*) total",
$sql
);

$countStmt=$conn->prepare($countSql);

$countStmt->bind_param(
    $types,
    ...$params
);

$countStmt->execute();

$totalRows=$countStmt
->get_result()
->fetch_assoc()['total'];

$totalPages=ceil($totalRows/$limit);

/* ===========================================================
   PAGINATION
=========================================================== */

$sql.=" ORDER BY a.applied_at DESC
LIMIT ?,?";

$types.="ii";

$params[]=$offset;
$params[]=$limit;

$stmt=$conn->prepare($sql);

$stmt->bind_param(
    $types,
    ...$params
);

$stmt->execute();

$result=$stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>View Applicants | HireConnect</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">

<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link rel="stylesheet" href="employer\css\view_applicant.css">

</head>

<body>

<?php include "includes/sidebar.php"; ?>

<div class="main-content">

<?php include "includes/topbar.php"; ?>

<div class="page-content">

<div class="container-fluid">

<!-- =========================
     Dashboard Statistics
========================= -->

<div class="row g-3 mb-4">

<div class="col-md-2">
<div class="card border-primary shadow-sm">
<div class="card-body text-center">
<h6>Total</h6>
<h3><?= $total ?></h3>
</div>
</div>
</div>

<div class="col-md-2">
<div class="card border-secondary shadow-sm">
<div class="card-body text-center">
<h6>Applied</h6>
<h3><?= $applied ?></h3>
</div>
</div>
</div>

<div class="col-md-2">
<div class="card border-warning shadow-sm">
<div class="card-body text-center">
<h6>Review</h6>
<h3><?= $review ?></h3>
</div>
</div>
</div>

<div class="col-md-2">
<div class="card border-info shadow-sm">
<div class="card-body text-center">
<h6>Shortlisted</h6>
<h3><?= $shortlisted ?></h3>
</div>
</div>
</div>

<div class="col-md-2">
<div class="card border-primary shadow-sm">
<div class="card-body text-center">
<h6>Interview</h6>
<h3><?= $interviews ?></h3>
</div>
</div>
</div>

<div class="col-md-2">
<div class="card border-success shadow-sm">
<div class="card-body text-center">
<h6>Hired</h6>
<h3><?= $hired ?></h3>
</div>
</div>
</div>

</div>

<!-- =========================
     Applicants Card
========================= -->

<div class="card shadow">

<div class="card-header bg-primary text-white d-flex justify-content-between">

<h4 class="mb-0">

<i class="bi bi-people-fill"></i>

Job Applicants

</h4>

<span>

<?= $totalRows ?> Applicant(s)

</span>

</div>

<div class="card-body">

<!-- =========================
     Search Form
========================= -->

<form method="GET" class="row g-3 mb-4">

<div class="col-md-4">

<input
type="text"
name="search"
id="searchApplicant"
class="form-control"
placeholder="Search applicant..."
value="<?= htmlspecialchars($search); ?>">

</div>

<div class="col-md-2">

<select
name="status"
class="form-select">

<option value="">All Status</option>

<?php

$statuses=[
"Applied",
"Under Review",
"Shortlisted",
"Interview Scheduled",
"Hired",
"Rejected"
];

foreach($statuses as $s){

?>

<option
value="<?= $s ?>"
<?= $status==$s ? "selected" : ""; ?>>

<?= $s ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-md-2">

<input
type="date"
name="from"
class="form-control"
value="<?= htmlspecialchars($from); ?>">

</div>

<div class="col-md-2">

<input
type="date"
name="to"
class="form-control"
value="<?= htmlspecialchars($to); ?>">

</div>

<div class="col-md-2">

<button class="btn btn-primary w-100">

<i class="bi bi-search"></i>

Search

</button>

</div>

</form>

<!-- =========================
     Bulk Form
========================= -->

<form method="POST" action="bulk_action.php">

<div class="table-responsive">

<table class="table table-hover table-bordered align-middle">

<thead class="table-dark">

<tr>

<th width="40">

<input
type="checkbox"
id="selectAll">

</th>

<th>Applicant</th>

<th>Email</th>

<th>Job</th>

<th>Status</th>

<th>Applied</th>

<th width="180">

Actions

</th>

</tr>

</thead>

<tbody id="applicantTable">

<?php

if($result->num_rows>0){

while($row=$result->fetch_assoc()){

?>

<tr>

<td>

<input
type="checkbox"
name="applications[]"
value="<?= $row['application_id']; ?>">

</td>

<td>

<strong>

<?= htmlspecialchars($row['full_name']); ?>

</strong>

</td>

<td>

<?= htmlspecialchars($row['email']); ?>

</td>

<td>

<?= htmlspecialchars($row['title']); ?>

</td>

<td>

<span class="badge bg-<?= $statusColors[$row['status']] ?? "dark"; ?>">

<?= htmlspecialchars($row['status']); ?>

</span>

</td>

<td>

<?= date("d M Y",strtotime($row['applied_at'])); ?>

</td>

<td>

<a
href="applicant_details.php?id=<?= $row['application_id']; ?>"
class="btn btn-primary btn-sm">

<i class="bi bi-eye"></i>

</a>

<?php if(!empty($row['cv_file'])){ ?>

<a
href="../uploads/cvs/<?= urlencode($row['cv_file']); ?>"
target="_blank"
class="btn btn-success btn-sm">

<i class="bi bi-file-earmark-pdf"></i>

</a>

<?php } ?>

<a
href="schedule_interview.php?id=<?= $row['application_id']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-calendar-event"></i>

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" class="text-center py-5">

<i
class="bi bi-inbox"
style="font-size:50px;"></i>

<h5 class="mt-3">

No applicants found.

</h5>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<!-- =========================
     Bulk Actions
========================= -->

<div class="row mt-3">

<div class="col-md-4">

<select
name="bulk_action"
class="form-select">

<option value="">

Bulk Action

</option>

<option value="Under Review">

Move to Review

</option>

<option value="Shortlisted">

Shortlist

</option>

<option value="Interview Scheduled">

Schedule Interview

</option>

<option value="Rejected">

Reject

</option>

<option value="Delete">

Delete

</option>

</select>

</div>

<div class="col-md-2">

<button
class="btn btn-success"
onclick="return confirm('Apply this action to selected applications?');">

Apply

</button>

</div>

</div>

</form>

<!-- =========================
     Pagination
========================= -->

<?php if($totalPages>1){ ?>

<nav class="mt-4">

<ul class="pagination justify-content-center">

<?php for($i=1;$i<=$totalPages;$i++){ ?>

<li class="page-item <?= $page==$i ? "active" : ""; ?>">

<a
class="page-link"
href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>&status=<?= urlencode($status); ?>&from=<?= $from; ?>&to=<?= $to; ?>">

<?= $i; ?>

</a>

</li>

<?php } ?>

</ul>

</nav>

<?php } ?>

</div>

</div>

</div>

</div>

<?php include "includes/footer.php"; ?>

</div>
<script>

// =====================================
// Select / Deselect All Checkboxes
// =====================================

const selectAll = document.getElementById("selectAll");

if (selectAll) {

    selectAll.addEventListener("change", function () {

        document.querySelectorAll("input[name='applications[]']").forEach(function (checkbox) {

            checkbox.checked = selectAll.checked;

        });

    });

}


// =====================================
// Live Applicant Search (AJAX)
// =====================================

const searchInput = document.getElementById("searchApplicant");

if (searchInput) {

    let timer;

    searchInput.addEventListener("keyup", function () {

        clearTimeout(timer);

        const search = this.value;

        timer = setTimeout(function () {

            fetch("search_applicants.php?search=" + encodeURIComponent(search))

                .then(response => response.text())

                .then(html => {

                    const table = document.getElementById("applicantTable");

                    if (table) {

                        table.innerHTML = html;

                    }

                })

                .catch(error => {

                    console.error("Search failed:", error);

                });

        }, 300); // Wait 300ms after typing

    });

}


// =====================================
// Confirm Bulk Action
// =====================================

const bulkForm = document.querySelector("form[action='bulk_action.php']");

if (bulkForm) {

    bulkForm.addEventListener("submit", function (e) {

        const checked = document.querySelectorAll("input[name='applications[]']:checked");

        const action = document.querySelector("select[name='bulk_action']").value;

        if (checked.length === 0) {

            e.preventDefault();

            alert("Please select at least one application.");

            return;

        }

        if (action === "") {

            e.preventDefault();

            alert("Please select a bulk action.");

            return;

        }

        if (!confirm("Apply this action to the selected applications?")) {

            e.preventDefault();

        }

    });

}


// =====================================
// Highlight Active Search
// =====================================

if (searchInput && searchInput.value.trim() !== "") {

    searchInput.classList.add("border-primary");

}


// =====================================
// Auto Hide Success / Error Alerts
// =====================================

setTimeout(function () {

    document.querySelectorAll(".alert").forEach(function (alert) {

        alert.classList.add("fade");

        setTimeout(() => alert.remove(), 500);

    });

}, 5000);

</script>

</body>
</html>