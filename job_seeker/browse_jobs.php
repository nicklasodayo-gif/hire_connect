<?php

require_once "../includes/jobseeker_auth.php";
require_once "../config/config.php";

$user_id = $_SESSION['user_id'];

/*=========================================================
SEARCH FILTERS
=========================================================*/

$search   = trim($_GET['search'] ?? "");
$category = trim($_GET['category'] ?? "");
$type     = trim($_GET['type'] ?? "");
$location = trim($_GET['location'] ?? "");

/*=========================================================
PAGINATION
=========================================================*/

$page = isset($_GET['page']) && is_numeric($_GET['page'])
        ? (int)$_GET['page']
        : 1;

$page = max($page,1);

$limit = 9;
$offset = ($page-1) * $limit;

/*=========================================================
STATISTICS
=========================================================*/

/* Open Jobs */

$stmt = $conn->prepare("
SELECT COUNT(*) total
FROM jobs
WHERE status='Open'
AND deadline >= CURDATE()
");

$stmt->execute();

$totalJobs = $stmt->get_result()->fetch_assoc()['total'];


/* Saved Jobs */

$stmt = $conn->prepare("
SELECT COUNT(*) total
FROM saved_jobs
WHERE user_id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$savedJobs = $stmt->get_result()->fetch_assoc()['total'];


/* Applied Jobs */

$stmt = $conn->prepare("
SELECT COUNT(*) total
FROM applications
WHERE applicant_id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$appliedJobs = $stmt->get_result()->fetch_assoc()['total'];


/* New Jobs */

$stmt = $conn->prepare("
SELECT COUNT(*) total
FROM jobs
WHERE status='Open'
AND created_at >= DATE_SUB(NOW(),INTERVAL 7 DAY)
");

$stmt->execute();

$newJobs = $stmt->get_result()->fetch_assoc()['total'];


/*=========================================================
LOAD JOB CATEGORIES
=========================================================*/

$categories = [];

$stmt = $conn->prepare("
SELECT DISTINCT category
FROM jobs
WHERE status='Open'
ORDER BY category
");

$stmt->execute();

$result = $stmt->get_result();

while($row = $result->fetch_assoc()){

    $categories[] = $row['category'];

}


/*=========================================================
MAIN QUERY
=========================================================*/

$sql = "

SELECT

    j.*,

    u.full_name AS employer_name,

    (

        SELECT COUNT(*)

        FROM applications a

        WHERE a.job_id=j.job_id

        AND a.applicant_id=?

    ) applied,

    (

        SELECT COUNT(*)

        FROM saved_jobs s

        WHERE s.job_id=j.job_id

        AND s.user_id=?

    ) saved,

    (

        SELECT COUNT(*)

        FROM applications ap

        WHERE ap.job_id=j.job_id

    ) total_applications

FROM jobs j

JOIN users u

ON u.user_id=j.employer_id

WHERE

j.status='Open'

AND

j.deadline>=CURDATE()

";

$types = "ii";

$params = [

$user_id,

$user_id

];


/*=========================================================
SEARCH
=========================================================*/

if($search != ""){

    $sql .= "

    AND(

        j.title LIKE ?

        OR

        u.full_name LIKE ?

    )

    ";

    $searchTerm = "%{$search}%";

    $types .= "ss";

    $params[] = $searchTerm;
    $params[] = $searchTerm;

}


/*=========================================================
CATEGORY
=========================================================*/

if($category != ""){

    $sql .= " AND j.category=?";

    $types .= "s";

    $params[] = $category;

}


/*=========================================================
EMPLOYMENT TYPE
=========================================================*/

if($type != ""){

    $sql .= " AND j.employment_type=?";

    $types .= "s";

    $params[] = $type;

}


/*=========================================================
LOCATION
=========================================================*/

if($location != ""){

    $sql .= " AND j.location LIKE ?";

    $types .= "s";

    $params[] = "%{$location}%";

}


/*=========================================================
COUNT RECORDS
=========================================================*/

$countSql = "

SELECT COUNT(*) total

FROM jobs j

JOIN users u

ON u.user_id=j.employer_id

WHERE

j.status='Open'

AND

j.deadline>=CURDATE()

";

$countTypes = "";

$countParams = [];

/* Search */

if($search != ""){

    $countSql .= "

    AND(

        j.title LIKE ?

        OR

        u.full_name LIKE ?

    )

    ";

    $countTypes .= "ss";

    $countParams[] = $searchTerm;
    $countParams[] = $searchTerm;

}

/* Category */

if($category != ""){

    $countSql .= " AND j.category=?";

    $countTypes .= "s";

    $countParams[] = $category;

}

/* Type */

if($type != ""){

    $countSql .= " AND j.employment_type=?";

    $countTypes .= "s";

    $countParams[] = $type;

}

/* Location */

if($location != ""){

    $countSql .= " AND j.location LIKE ?";

    $countTypes .= "s";

    $countParams[] = "%{$location}%";

}

$countStmt = $conn->prepare($countSql);

if(!empty($countParams)){

    $countStmt->bind_param(

        $countTypes,

        ...$countParams

    );

}

$countStmt->execute();

$totalRows = $countStmt
->get_result()
->fetch_assoc()['total'];

$totalPages = ceil($totalRows/$limit);


/*=========================================================
PAGINATION
=========================================================*/

$sql .= "

ORDER BY

j.created_at DESC

LIMIT ?,?

";

$types .= "ii";

$params[] = $offset;
$params[] = $limit;

$stmt = $conn->prepare($sql);

$stmt->bind_param(

$types,

...$params

);

$stmt->execute();

$jobs = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Browse Jobs | HireConnect</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">

<link
rel="stylesheet"
href="job_seeker\css\browse_jobs.css">

</head>

<body>

<?php include "includes/sidebar.php"; ?>

<div class="main-content">

<?php include "includes/topbar.php"; ?>

<div class="container-fluid py-4">

<!-- =========================================
PAGE TITLE
========================================= -->

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2 class="fw-bold">

<i class="bi bi-search"></i>

Browse Jobs

</h2>

<p class="text-muted mb-0">

Discover your next career opportunity.

</p>

</div>

</div>

<!-- =========================================
STATISTICS
========================================= -->

<div class="row g-3 mb-4">

<div class="col-lg-3 col-md-6">

<div class="card shadow border-0">

<div class="card-body text-center">

<i class="bi bi-briefcase-fill fs-1 text-primary"></i>

<h3 class="mt-2">

<?= $totalJobs; ?>

</h3>

<p class="text-muted mb-0">

Open Jobs

</p>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card shadow border-0">

<div class="card-body text-center">

<i class="bi bi-bookmark-fill fs-1 text-warning"></i>

<h3 class="mt-2">

<?= $savedJobs; ?>

</h3>

<p class="text-muted mb-0">

Saved Jobs

</p>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card shadow border-0">

<div class="card-body text-center">

<i class="bi bi-send-check-fill fs-1 text-success"></i>

<h3 class="mt-2">

<?= $appliedJobs; ?>

</h3>

<p class="text-muted mb-0">

Applications

</p>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card shadow border-0">

<div class="card-body text-center">

<i class="bi bi-stars fs-1 text-danger"></i>

<h3 class="mt-2">

<?= $newJobs; ?>

</h3>

<p class="text-muted mb-0">

New This Week

</p>

</div>

</div>

</div>

</div>

<!-- =========================================
SEARCH FILTER
========================================= -->

<div class="card shadow mb-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="bi bi-funnel-fill"></i>

Find Your Ideal Job

</h5>

</div>

<div class="card-body">

<form method="GET">

<div class="row g-3">

<div class="col-lg-3">

<input
type="text"
name="search"
class="form-control"
placeholder="Job title or employer"
value="<?= htmlspecialchars($search); ?>">

</div>

<div class="col-lg-2">

<select
name="category"
class="form-select">

<option value="">

All Categories

</option>

<?php foreach($categories as $cat){ ?>

<option
value="<?= htmlspecialchars($cat); ?>"
<?= ($category==$cat) ? "selected" : ""; ?>>

<?= htmlspecialchars($cat); ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-lg-2">

<select
name="type"
class="form-select">

<option value="">

Employment Type

</option>

<option
value="Full-Time"
<?= ($type=="Full-Time") ? "selected" : ""; ?>>

Full-Time

</option>

<option
value="Part-Time"
<?= ($type=="Part-Time") ? "selected" : ""; ?>>

Part-Time

</option>

<option
value="Internship"
<?= ($type=="Internship") ? "selected" : ""; ?>>

Internship

</option>

<option
value="Remote"
<?= ($type=="Remote") ? "selected" : ""; ?>>

Remote

</option>

<option
value="Contract"
<?= ($type=="Contract") ? "selected" : ""; ?>>

Contract

</option>

</select>

</div>

<div class="col-lg-3">

<input
type="text"
name="location"
class="form-control"
placeholder="Location"
value="<?= htmlspecialchars($location); ?>">

</div>

<div class="col-lg-2 d-grid">

<button
type="submit"
class="btn btn-primary">

<i class="bi bi-search"></i>

Search

</button>

</div>

</div>

</form>

</div>

</div>

<!-- =========================================
JOB LIST
========================================= -->

<div class="row">

<?php

if($jobs->num_rows > 0){

while($job = $jobs->fetch_assoc()){

    /*=========================================
    COMPANY LOGO
    =========================================*/

    $logo = "../uploads/logos/default-company.png";

    if(
        !empty($job['logo']) &&
        file_exists("../uploads/logos/".$job['logo'])
    ){

        $logo = "../uploads/logos/".$job['logo'];

    }

    /*=========================================
    JOB STATUS BADGE
    =========================================*/

    $today = strtotime(date("Y-m-d"));

    $deadline = strtotime($job['deadline']);

    $daysLeft = floor(($deadline-$today)/86400);

    if($daysLeft <= 3){

        $badge = "<span class='badge bg-danger'>Closing Soon</span>";

    }elseif($daysLeft <= 7){

        $badge = "<span class='badge bg-warning text-dark'>Urgent</span>";

    }else{

        $badge = "<span class='badge bg-success'>Open</span>";

    }

    /*=========================================
    SALARY FORMAT
    =========================================*/

    if(is_numeric($job['salary'])){

        $salary = "KES ".number_format($job['salary']);

    }else{

        $salary = htmlspecialchars($job['salary']);

    }

    /*=========================================
    POSTED TIME
    =========================================*/

    $posted = strtotime($job['created_at']);

    $seconds = time() - $posted;

    if($seconds < 3600){

        $postedText = floor($seconds/60)." mins ago";

    }elseif($seconds < 86400){

        $postedText = floor($seconds/3600)." hrs ago";

    }elseif($seconds < 604800){

        $postedText = floor($seconds/86400)." days ago";

    }else{

        $postedText = date("d M Y",$posted);

    }

?>

<div class="col-lg-4 col-md-6 mb-4">

<div class="card job-card shadow-sm border-0 h-100">

<img
src="<?= $logo; ?>"
class="card-img-top"
style="height:180px;object-fit:contain;padding:20px;"
alt="Company Logo">

<div class="card-body">

<div class="d-flex justify-content-between align-items-start mb-2">

<h5 class="fw-bold mb-0">

<?= htmlspecialchars($job['title']); ?>

</h5>

<?= $badge; ?>

</div>

<p class="text-muted mb-2">

<i class="bi bi-building"></i>

<?= htmlspecialchars($job['employer_name']); ?>

</p>

<p>

<i class="bi bi-tag-fill text-primary"></i>

<?= htmlspecialchars($job['category']); ?>

</p>

<p>

<i class="bi bi-geo-alt-fill text-danger"></i>

<?= htmlspecialchars($job['location']); ?>

</p>

<p>

<i class="bi bi-clock-fill text-success"></i>

<?= htmlspecialchars($job['employment_type']); ?>

</p>

<p>

<i class="bi bi-cash-stack text-warning"></i>

<strong><?= $salary; ?></strong>

</p>

<p>

<i class="bi bi-people-fill text-info"></i>

<?= $job['total_applications']; ?>

Applicant(s)

</p>

<p>

<i class="bi bi-calendar-event"></i>

Deadline:

<strong>

<?= date("d M Y",strtotime($job['deadline'])); ?>

</strong>

</p>

<p class="small text-muted">

<?= substr(strip_tags($job['description']),0,140); ?>...

</p>

</div>

<div class="card-footer bg-white">

<div class="d-grid gap-2">

<a
href="job_details.php?id=<?= $job['job_id']; ?>"
class="btn btn-primary">

<i class="bi bi-eye"></i>

View Details

</a>

<?php if($job['saved'] == 0){ ?>

<a
href="saved_jobs.php?save=<?= $job['job_id']; ?>"
class="btn btn-outline-warning">

<i class="bi bi-bookmark-plus"></i>

Save Job

</a>

<?php }else{ ?>

<button
class="btn btn-warning"
disabled>

<i class="bi bi-bookmark-check-fill"></i>

Saved

</button>

<?php } ?>

<?php if($job['applied'] == 0){ ?>

<a
href="apply_job.php?id=<?= $job['job_id']; ?>"
class="btn btn-success">

<i class="bi bi-send-fill"></i>

Apply Now

</a>

<?php }else{ ?>

<button
class="btn btn-secondary"
disabled>

<i class="bi bi-check-circle-fill"></i>

Already Applied

</button>

<?php } ?>

</div>

</div>

<div class="card-footer bg-light">

<small class="text-muted">

Posted

<strong>

<?= $postedText; ?>

</strong>

</small>

</div>

</div>

</div>

<?php

}

}else{

?>

<div class="col-12">

<div class="card shadow">

<div class="card-body text-center py-5">

<i class="bi bi-search display-1 text-secondary"></i>

<h3 class="mt-3">

No Jobs Found

</h3>

<p class="text-muted">

Try changing your search filters.

</p>

<a
href="browse_jobs.php"
class="btn btn-primary">

<i class="bi bi-arrow-repeat"></i>

Reset Filters

</a>

</div>

</div>

</div>

<?php

}

?>

</div>

<!-- ==========================================
PAGINATION
========================================== -->

<?php if($totalPages > 1){ ?>

<nav class="mt-4">

<ul class="pagination justify-content-center">

<!-- Previous -->

<li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">

<a
class="page-link"
href="?page=<?= $page-1; ?>&search=<?= urlencode($search); ?>&category=<?= urlencode($category); ?>&type=<?= urlencode($type); ?>&location=<?= urlencode($location); ?>">

<i class="bi bi-chevron-left"></i>

</a>

</li>

<?php for($i=1; $i<=$totalPages; $i++){ ?>

<li class="page-item <?= ($page==$i) ? 'active' : ''; ?>">

<a
class="page-link"
href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>&category=<?= urlencode($category); ?>&type=<?= urlencode($type); ?>&location=<?= urlencode($location); ?>">

<?= $i; ?>

</a>

</li>

<?php } ?>

<!-- Next -->

<li class="page-item <?= ($page >= $totalPages) ? 'disabled' : ''; ?>">

<a
class="page-link"
href="?page=<?= $page+1; ?>&search=<?= urlencode($search); ?>&category=<?= urlencode($category); ?>&type=<?= urlencode($type); ?>&location=<?= urlencode($location); ?>">

<i class="bi bi-chevron-right"></i>

</a>

</li>

</ul>

</nav>

<?php } ?>

</div>

<?php include "includes/footer.php"; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

/*=========================================
AUTO FOCUS SEARCH
=========================================*/

const searchBox = document.querySelector("input[name='search']");

if(searchBox && searchBox.value !== ""){

    searchBox.focus();

}

/*=========================================
SUBMIT SEARCH ON ENTER
=========================================*/

document.querySelectorAll("input").forEach(function(input){

    input.addEventListener("keypress",function(e){

        if(e.key === "Enter"){

            this.form.submit();

        }

    });

});

/*=========================================
JOB CARD HOVER EFFECT
=========================================*/

document.querySelectorAll(".job-card").forEach(function(card){

    card.addEventListener("mouseenter",function(){

        this.classList.add("shadow-lg");

    });

    card.addEventListener("mouseleave",function(){

        this.classList.remove("shadow-lg");

    });

});

/*=========================================
BOOTSTRAP TOOLTIPS
=========================================*/

const tooltipTriggerList = [].slice.call(

document.querySelectorAll('[data-bs-toggle="tooltip"]')

);

tooltipTriggerList.map(function(el){

    return new bootstrap.Tooltip(el);

});

/*=========================================
SMOOTH SCROLL TO TOP
=========================================*/

window.addEventListener("load",function(){

    window.scrollTo({

        top:0,

        behavior:"smooth"

    });

});

/*=========================================
LOADING EFFECT
=========================================*/

document.querySelectorAll("form").forEach(function(form){

    form.addEventListener("submit",function(){

        const btn = this.querySelector("button[type='submit']");

        if(btn){

            btn.disabled = true;

            btn.innerHTML =
            '<span class="spinner-border spinner-border-sm"></span> Loading...';

        }

    });

});

</script>

</body>

</html>