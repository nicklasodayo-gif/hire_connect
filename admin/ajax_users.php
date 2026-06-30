<?php
session_start();
require_once "../includes/admin_auth.php";
require_once "../config/config.php";

/*==================================================
GET FILTERS
==================================================*/

$search = trim($_GET['search'] ?? "");
$role = trim($_GET['role'] ?? "");
$status = trim($_GET['status'] ?? "");

$sort = $_GET['sort'] ?? "newest";

$page = isset($_GET['page']) && is_numeric($_GET['page'])
    ? max(1, (int)$_GET['page'])
    : 1;

$limit = isset($_GET['limit']) && is_numeric($_GET['limit'])
    ? max(5, (int)$_GET['limit'])
    : 10;

$allowedLimits = [5,10,25,50,100];

if(!in_array($limit,$allowedLimits)){
    $limit = 10;
}

$offset = ($page - 1) * $limit;

/*==================================================
WHERE CLAUSE
==================================================*/

$where = " WHERE 1=1 ";

$params = [];
$types = "";

/* ==========================================
   Statistics
========================================== */

$totalUsers = $conn->query("
SELECT COUNT(*) total
FROM users
")->fetch_assoc()['total'];

$totalEmployers = $conn->query("
SELECT COUNT(*) total
FROM users
WHERE role='employer'
")->fetch_assoc()['total'];

$totalApplicants = $conn->query("
SELECT COUNT(*) total
FROM users
WHERE role='jobseeker'
")->fetch_assoc()['total'];

$totalAdmins = $conn->query("
SELECT COUNT(*) total
FROM users
WHERE role='admin'
")->fetch_assoc()['total'];

/*==================================================
SEARCH
==================================================*/

if($search != ""){

    $where .= "
        AND
        (
            user_id LIKE ?
            OR full_name LIKE ?
            OR email LIKE ?
            OR phone LIKE ?
        )
    ";

    $searchTerm = "%{$search}%";

    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;

    $types .= "ssss";

}

/*==================================================
ROLE FILTER
==================================================*/

if($role != ""){

    $where .= " AND role = ? ";

    $params[] = $role;

    $types .= "s";

}

/*==================================================
STATUS FILTER
==================================================*/

if($status != ""){

    $where .= " AND status = ? ";

    $params[] = $status;

    $types .= "s";

}

/*==================================================
SORTING
==================================================*/

$orderBy = "ORDER BY user_id DESC";

switch($sort){

    case "oldest":
        $orderBy = "ORDER BY user_id ASC";
        break;

    case "name_asc":
        $orderBy = "ORDER BY full_name ASC";
        break;

    case "name_desc":
        $orderBy = "ORDER BY full_name DESC";
        break;

    case "email":
        $orderBy = "ORDER BY email ASC";
        break;

    case "role":
        $orderBy = "ORDER BY role ASC";
        break;

    case "newest":
    default:
        $orderBy = "ORDER BY user_id DESC";
        break;

}

/*==================================================
COUNT USERS
==================================================*/

$countSQL = "
SELECT COUNT(*) AS total
FROM users
{$where}
";

$stmt = $conn->prepare($countSQL);

if(!empty($params)){
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$totalUsers = (int)$stmt
    ->get_result()
    ->fetch_assoc()['total'];

$totalPages = max(1, ceil($totalUsers / $limit));

if($page > $totalPages){

    $page = $totalPages;

    $offset = ($page - 1) * $limit;

}

/*==================================================
GET USERS
==================================================*/

$sql = "
SELECT *
FROM users

{$where}

{$orderBy}

LIMIT ?, ?
";

$params2 = $params;

$params2[] = $offset;
$params2[] = $limit;

$types2 = $types . "ii";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    $types2,
    ...$params2
);

$stmt->execute();

$users = $stmt->get_result();

/*==================================================
STATISTICS
==================================================*/

$stats = [];

$result = $conn->query("
SELECT
COUNT(*) total,
SUM(role='jobseeker') jobseekers,
SUM(role='employer') employers,
SUM(role='admin') admins
FROM users
");

if($result){

    $stats = $result->fetch_assoc();

}

/*==================================================
HELPER FUNCTIONS
==================================================*/

function badgeColor($role){

    switch(strtolower($role)){

        case "admin":
            return "danger";

        case "employer":
            return "primary";

        case "jobseeker":
            return "success";

        default:
            return "secondary";

    }

}

function statusColor($status){

    switch(strtolower($status ?? "")){

        case "active":
            return "success";

        case "pending":
            return "warning";

        case "suspended":
            return "danger";

        default:
            return "secondary";

    }

}
?>

<div class="row mb-4">

    <div class="col-md-3">

        <div class="card border-primary shadow-sm">

            <div class="card-body">

                <h6 class="text-muted">

                    Total Users

                </h6>

                <h2 class="text-primary">

                    <?= $totalUsers; ?>

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card border-success shadow-sm">

            <div class="card-body">

                <h6 class="text-muted">

                    Employers

                </h6>

                <h2 class="text-success">

                    <?= $totalEmployers; ?>

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card border-info shadow-sm">

            <div class="card-body">

                <h6 class="text-muted">

                    Job Seekers

                </h6>

                <h2 class="text-info">

                    <?= $totalApplicants; ?>

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card border-danger shadow-sm">

            <div class="card-body">

                <h6 class="text-muted">

                    Administrators

                </h6>

                <h2 class="text-danger">

                    <?= $totalAdmins; ?>

                </h2>

            </div>

        </div>

    </div>

</div>

<!-- ==========================================
USERS TABLE
========================================== -->

<div class="card shadow border-0">

<div class="card-header bg-primary text-white">

<div class="d-flex justify-content-between align-items-center">

<div>

<h5 class="mb-0">

<i class="bi bi-people-fill"></i>

Users

</h5>

<small>

Showing
<strong><?= $users->num_rows ?></strong>
of
<strong><?= $totalUsers ?></strong>
users

</small>

</div>

<div class="text-end">

<span class="badge bg-light text-dark">

Total:
<?= $stats['total'] ?? 0 ?>

</span>

<span class="badge bg-success">

Job Seekers:
<?= $stats['jobseekers'] ?? 0 ?>

</span>

<span class="badge bg-primary">

Employers:
<?= $stats['employers'] ?? 0 ?>

</span>

<span class="badge bg-danger">

Admins:
<?= $stats['admins'] ?? 0 ?>

</span>

</div>

</div>

</div>

<div class="card-body p-0">

<?php if($users->num_rows > 0){ ?>

<div class="table-responsive">

<table class="table table-hover align-middle mb-0">

<thead class="table-light">

<tr>

<th width="70">

ID

</th>

<th>

User

</th>

<th>

Contact

</th>

<th>

Role

</th>

<th>

Status

</th>

<th>

Joined

</th>

<th>

Last Login

</th>

<th class="text-center">

Actions

</th>

</tr>

</thead>

<tbody>

<?php while($row = $users->fetch_assoc()){ ?>

<?php

$image = "../assets/images/default.png";

if(
!empty($row['profile_picture']) &&
file_exists("../uploads/profiles/".$row['profile_picture'])
){

$image = "../uploads/profiles/".$row['profile_picture'];

}

?>

<tr>

<td>

<strong>

#<?= $row['user_id'] ?>

</strong>

</td>

<td>

<div class="d-flex align-items-center">

<img
src="<?= $image ?>"
width="55"
height="55"
class="rounded-circle border me-3"
style="object-fit:cover;">

<div>

<div class="fw-bold">

<?= htmlspecialchars($row['full_name']) ?>

</div>

<small class="text-muted">

<?= htmlspecialchars($row['email']) ?>

</small>

<?php if(isset($row['email_verified'])){ ?>

<div class="mt-1">

<?php if($row['email_verified']){ ?>

<span class="badge bg-success">

Verified

</span>

<?php }else{ ?>

<span class="badge bg-warning text-dark">

Not Verified

</span>

<?php } ?>

</div>

<?php } ?>

</div>

</div>

</td>

<td>

<div>

<i class="bi bi-envelope"></i>

<?= htmlspecialchars($row['email']) ?>

</div>

<div class="text-muted">

<i class="bi bi-telephone"></i>

<?= htmlspecialchars($row['phone']) ?>

</div>

</td>

<td>

<span class="badge bg-<?= badgeColor($row['role']) ?>">

<?= ucfirst($row['role']) ?>

</span>

</td>

<td>

<?php if(isset($row['status'])){ ?>

<span class="badge bg-<?= statusColor($row['status']) ?>">

<?= ucfirst($row['status']) ?>

</span>

<?php }else{ ?>

<span class="badge bg-secondary">

Unknown

</span>

<?php } ?>

</td>

<td>

<?= date("d M Y",strtotime($row['created_at'])) ?>

<br>

<small class="text-muted">

<?= date("g:i A",strtotime($row['created_at'])) ?>

</small>

</td>

<td>

<?php if(!empty($row['last_login'])){ ?>

<?= date("d M Y",strtotime($row['last_login'])) ?>

<br>

<small class="text-muted">

<?= date("g:i A",strtotime($row['last_login'])) ?>

</small>

<?php }else{ ?>

<span class="text-muted">

Never

</span>

<?php } ?>

</td>

<td class="text-center">

<?php } ?>

</tbody>

</table>

</div>

<?php }else{ ?>

<div class="text-center py-5">

<i class="bi bi-people display-1 text-muted"></i>

<h4 class="mt-3">

No users found

</h4>

<p class="text-muted">

No users matched your current search or filter.

</p>

<button
class="btn btn-outline-primary"
onclick="loadUsers(1)">

<i class="bi bi-arrow-clockwise"></i>

Refresh

</button>

</div>

<?php } ?>

</div>

</div>

<div class="dropdown">

<button
class="btn btn-sm btn-outline-primary dropdown-toggle"
type="button"
data-bs-toggle="dropdown">

<i class="bi bi-three-dots"></i>

Actions

</button>

<ul class="dropdown-menu dropdown-menu-end">

<li>

<a
class="dropdown-item"
href="view_user.php?id=<?= $row['user_id'] ?>">

<i class="bi bi-eye text-primary"></i>

View Profile

</a>

</li>

<li>

<a
class="dropdown-item"
href="edit_user.php?id=<?= $row['user_id'] ?>">

<i class="bi bi-pencil-square text-warning"></i>

Edit User

</a>

</li>

<li><hr class="dropdown-divider"></li>

<?php if(isset($row['status'])){ ?>

<?php if($row['status']=="active"){ ?>

<li>

<a
class="dropdown-item text-warning"
href="toggle_user_status.php?id=<?= $row['user_id'] ?>&status=suspended">

<i class="bi bi-pause-circle"></i>

Suspend Account

</a>

</li>

<?php }else{ ?>

<li>

<a
class="dropdown-item text-success"
href="toggle_user_status.php?id=<?= $row['user_id'] ?>&status=active">

<i class="bi bi-check-circle"></i>

Activate Account

</a>

</li>

<?php } ?>

<?php } ?>

<li><hr class="dropdown-divider"></li>

<li>

<?php if($row['user_id'] != $_SESSION['user_id']){ ?>

<a
href="delete_user.php?id=<?= $row['user_id']; ?>"
class="btn btn-sm btn-danger">

<i class="bi bi-trash"></i>

</a>

<?php } ?>

Delete User



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

<div class="row mt-4 align-items-center">

<div class="col-md-4">

<p class="text-muted mb-0">

Showing

<strong>

<?= ($offset + 1) ?>

</strong>

to

<strong>

<?= min($offset + $limit, $totalUsers) ?>

</strong>

of

<strong>

<?= $totalUsers ?>

</strong>

users

</p>

</div>

<div class="col-md-4 text-center">

<nav>

<ul class="pagination justify-content-center mb-0">

<li class="page-item <?= $page<=1 ? 'disabled' : '' ?>">

<a
class="page-link"
href="#"
onclick="loadUsers(<?= $page-1 ?>);return false;">

Previous

</a>

</li>

<?php

$start=max(1,$page-2);
$end=min($totalPages,$page+2);

for($i=$start;$i<=$end;$i++){

?>

<li class="page-item <?= $page==$i?'active':'' ?>">

<a
class="page-link"
href="#"
onclick="loadUsers(<?= $i ?>);return false;">

<?= $i ?>

</a>

</li>

<?php } ?>

<li class="page-item <?= $page>=$totalPages ? 'disabled' : '' ?>">

<a
class="page-link"
href="#"
onclick="loadUsers(<?= $page+1 ?>);return false;">

Next

</a>

</li>

</ul>

</nav>

</div>

<div class="col-md-4 text-end">

<div class="d-inline-flex align-items-center">

<label class="me-2">

Show

</label>

<select
id="recordsPerPage"
class="form-select form-select-sm"
style="width:90px;"
onchange="loadUsers(1)">

<option value="5" <?= $limit==5?'selected':'' ?>>

5

</option>

<option value="10" <?= $limit==10?'selected':'' ?>>

10

</option>

<option value="25" <?= $limit==25?'selected':'' ?>>

25

</option>

<option value="50" <?= $limit==50?'selected':'' ?>>

50

</option>

<option value="100" <?= $limit==100?'selected':'' ?>>

100

</option>

</select>

</div>

</div>

</div>

<!-- ==========================================
FILTERS & SEARCH
========================================== -->

<div class="card shadow border-0 mt-4">

<div class="card-header bg-light">

<h5 class="mb-0">

<i class="bi bi-funnel"></i>

Search & Filters

</h5>

</div>

<div class="card-body">

<div class="row g-3">

<div class="col-lg-3">

<label class="form-label">

Search

</label>

<input
type="text"
id="search"
class="form-control"
placeholder="Name, email, phone..."
value="<?= htmlspecialchars($search) ?>">

</div>

<div class="col-lg-2">

<label class="form-label">

Role

</label>

<select
id="role"
class="form-select">

<option value="">All Roles</option>

<option value="jobseeker" <?= $role=="jobseeker"?"selected":"" ?>>

Job Seeker

</option>

<option value="employer" <?= $role=="employer"?"selected":"" ?>>

Employer

</option>

<option value="admin" <?= $role=="admin"?"selected":"" ?>>

Administrator

</option>

</select>

</div>

<div class="col-lg-2">

<label class="form-label">

Status

</label>

<select
id="status"
class="form-select">

<option value="">All Status</option>

<option value="active" <?= $status=="active"?"selected":"" ?>>

Active

</option>

<option value="pending" <?= $status=="pending"?"selected":"" ?>>

Pending

</option>

<option value="suspended" <?= $status=="suspended"?"selected":"" ?>>

Suspended

</option>

</select>

</div>

<div class="col-lg-2">

<label class="form-label">

Sort By

</label>

<select
id="sort"
class="form-select">

<option value="newest" <?= $sort=="newest"?"selected":"" ?>>

Newest

</option>

<option value="oldest" <?= $sort=="oldest"?"selected":"" ?>>

Oldest

</option>

<option value="name_asc" <?= $sort=="name_asc"?"selected":"" ?>>

Name (A-Z)

</option>

<option value="name_desc" <?= $sort=="name_desc"?"selected":"" ?>>

Name (Z-A)

</option>

<option value="email" <?= $sort=="email"?"selected":"" ?>>

Email

</option>

<option value="role" <?= $sort=="role"?"selected":"" ?>>

Role

</option>

</select>

</div>

<div class="col-lg-3 d-flex align-items-end">

<button
class="btn btn-primary me-2"
onclick="loadUsers(1)">

<i class="bi bi-search"></i>

Search

</button>

<button
class="btn btn-outline-secondary"
onclick="resetFilters()">

<i class="bi bi-arrow-clockwise"></i>

Reset

</button>

</div>

</div>

</div>

</div>

<!-- ==========================================
LOADING SPINNER
========================================== -->

<div
id="usersLoading"
class="text-center my-4"
style="display:none;">

<div
class="spinner-border text-primary"
role="status">

<span class="visually-hidden">

Loading...

</span>

</div>

<p class="mt-2">

Loading users...

</p>

</div>

<!-- ==========================================
JAVASCRIPT
========================================== -->

<script>

let searchTimer = null;

/*=========================================
AUTO SEARCH
=========================================*/

const searchInput = document.getElementById("search");

if(searchInput){

searchInput.addEventListener("keyup", function(){

clearTimeout(searchTimer);

searchTimer = setTimeout(function(){

loadUsers(1);

},500);

});

}

/*=========================================
AUTO FILTERS
=========================================*/

["role","status","sort","recordsPerPage"].forEach(function(id){

const element=document.getElementById(id);

if(element){

element.addEventListener("change",function(){

loadUsers(1);

});

}

});

/*=========================================
RESET FILTERS
=========================================*/

function resetFilters(){

document.getElementById("search").value="";

document.getElementById("role").value="";

document.getElementById("status").value="";

document.getElementById("sort").value="newest";

const limit=document.getElementById("recordsPerPage");

if(limit){

limit.value="10";

}

loadUsers(1);

}

/*=========================================
AJAX LOADER
=========================================*/

function loadUsers(page=1){

const loading=document.getElementById("usersLoading");

if(loading){

loading.style.display="block";

}

const search=document.getElementById("search")?.value || "";

const role=document.getElementById("role")?.value || "";

const status=document.getElementById("status")?.value || "";

const sort=document.getElementById("sort")?.value || "newest";

const limit=document.getElementById("recordsPerPage")?.value || 10;

const params=new URLSearchParams({

page:page,

search:search,

role:role,

status:status,

sort:sort,

limit:limit

});

fetch("load_users.php?"+params.toString())

.then(response=>response.text())

.then(html=>{

const container=document.getElementById("usersContainer");

if(container){

container.innerHTML=html;

}

})

.catch(error=>{

console.error(error);

})

.finally(()=>{

if(loading){

loading.style.display="none";

}

});

}

</script>