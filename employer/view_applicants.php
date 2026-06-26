<?php
include "includes/auth.php";
include "../connect.php";

$employer_id = $_SESSION['user_id'];

$search = "";

$sql = "
SELECT
    a.application_id,
    a.status,
    a.cv_file,
    a.applied_at,

    u.user_id,
    u.fullname,
    u.email,

    j.job_id,
    j.title

FROM applications a

INNER JOIN users u
ON a.applicant_id = u.user_id

INNER JOIN jobs j
ON a.job_id = j.job_id

WHERE j.employer_id = '$employer_id'
";

if(isset($_GET['search']) && !empty($_GET['search'])){

    $search = mysqli_real_escape_string(
        $conn,
        trim($_GET['search'])
    );

    $sql .= "
    AND (
        u.fullname LIKE '%$search%'
        OR u.email LIKE '%$search%'
        OR j.title LIKE '%$search%'
    )
    ";
}

$sql .= "
ORDER BY a.applied_at DESC
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>View Applicants | HireConnect</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link rel="stylesheet"
href="assets/css/employer.css">

</head>

<body>

<?php include "includes/sidebar.php"; ?>

<div class="main-content">

    <?php include "includes/topbar.php"; ?>

    <div class="page-content">

        <div class="container-fluid">

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">

                    <div class="d-flex justify-content-between align-items-center">

                        <h4 class="mb-0">
                            <i class="bi bi-people-fill"></i>
                            Job Applicants
                        </h4>

                        <span>
                            Total Applicants:
                            <?= mysqli_num_rows($result); ?>
                        </span>

                    </div>

                </div>

                <div class="card-body">

                    <!-- Search Form -->

                    <form method="GET" class="mb-4">

                        <div class="row">

                            <div class="col-md-10">

                                <input
                                    type="text"
                                    name="search"
                                    class="form-control"
                                    placeholder="Search applicant, email or job title..."
                                    value="<?= htmlspecialchars($search) ?>">

                            </div>

                            <div class="col-md-2">

                                <button
                                    class="btn btn-primary w-100">

                                    <i class="bi bi-search"></i>
                                    Search

                                </button>

                            </div>

                        </div>

                    </form>

                    <!-- Applicants Table -->

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-dark">

                                <tr>

                                    <th>#</th>
                                    <th>Applicant</th>
                                    <th>Email</th>
                                    <th>Job Title</th>
                                    <th>Status</th>
                                    <th>Applied</th>
                                    <th width="220">Actions</th>

                                </tr>

                            </thead>

                            <tbody>

                            <?php

                            if(mysqli_num_rows($result) > 0){

                                $count = 1;

                                while($row = mysqli_fetch_assoc($result)){

                            ?>

                                <tr>

                                    <td>
                                        <?= $count++ ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['fullname']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['email']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['title']) ?>
                                    </td>

                                    <td>

                                        <?php

                                        switch($row['status']){

                                            case 'Pending':
                                                echo '<span class="badge bg-warning">Pending</span>';
                                                break;

                                            case 'Reviewed':
                                                echo '<span class="badge bg-info">Reviewed</span>';
                                                break;

                                            case 'Shortlisted':
                                                echo '<span class="badge bg-primary">Shortlisted</span>';
                                                break;

                                            case 'Rejected':
                                                echo '<span class="badge bg-danger">Rejected</span>';
                                                break;

                                            case 'Hired':
                                                echo '<span class="badge bg-success">Hired</span>';
                                                break;

                                            default:
                                                echo '<span class="badge bg-secondary">Unknown</span>';
                                        }

                                        ?>

                                    </td>

                                    <td>
                                        <?= date(
                                            "d M Y",
                                            strtotime($row['applied_at'])
                                        ) ?>
                                    </td>

                                    <td>

                                        <!-- View Details -->

                                        <a
                                            href="applicant_details.php?id=<?= $row['application_id'] ?>"
                                            class="btn btn-primary btn-sm">

                                            <i class="bi bi-eye"></i>

                                        </a>

                                        <!-- CV -->

                                        <?php if(!empty($row['cv_file'])){ ?>

                                        <a
                                            href="../uploads/cvs/<?= $row['cv_file'] ?>"
                                            target="_blank"
                                            class="btn btn-success btn-sm">

                                            <i class="bi bi-file-earmark-pdf"></i>

                                        </a>

                                        <?php } ?>

                                        <!-- Interview -->

                                        <a
                                            href="schedule_interview.php?id=<?= $row['application_id'] ?>"
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

                                    <td colspan="7" class="text-center">

                                        <div class="py-4">

                                            <i class="bi bi-inbox"
                                               style="font-size:40px;"></i>

                                            <p class="mt-3 mb-0">

                                                No applicants found.

                                            </p>

                                        </div>

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