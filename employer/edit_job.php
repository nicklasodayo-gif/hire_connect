<?php
include "includes/auth.php";
include "../connect.php";

$employer_id = $_SESSION['user_id'];

if(!isset($_GET['id'])){
    header("Location: manage_jobs.php");
    exit();
}

$job_id = (int)$_GET['id'];

$query = mysqli_query(
    $conn,
    "SELECT *
     FROM jobs
     WHERE job_id='$job_id'
     AND employer_id='$employer_id'"
);

if(mysqli_num_rows($query) == 0){
    header("Location: manage_jobs.php");
    exit();
}

$job = mysqli_fetch_assoc($query);

$message = "";

if(isset($_POST['update_job'])){

    $title = mysqli_real_escape_string(
        $conn,
        $_POST['title']
    );

    $category = mysqli_real_escape_string(
        $conn,
        $_POST['category']
    );

    $employment_type = mysqli_real_escape_string(
        $conn,
        $_POST['employment_type']
    );

    $location = mysqli_real_escape_string(
        $conn,
        $_POST['location']
    );

    $salary = mysqli_real_escape_string(
        $conn,
        $_POST['salary']
    );

    $deadline = $_POST['deadline'];

    $description = mysqli_real_escape_string(
        $conn,
        $_POST['description']
    );

    $requirements = mysqli_real_escape_string(
        $conn,
        $_POST['requirements']
    );

    $logo_name = $job['logo'];

    if(!empty($_FILES['company_logo']['name'])){

        $logo_name =
            time() . "_" .
            basename($_FILES['company_logo']['name']);

        move_uploaded_file(
            $_FILES['company_logo']['tmp_name'],
            "../uploads/logos/" . $logo_name
        );
    }

    $update = mysqli_query(
        $conn,
        "UPDATE jobs SET

            title='$title',
            category='$category',
            employment_type='$employment_type',
            location='$location',
            salary='$salary',
            deadline='$deadline',
            description='$description',
            requirements='$requirements',
            logo='$logo_name'

        WHERE job_id='$job_id'
        AND employer_id='$employer_id'"
    );

    if($update){

        $message = "
        <div class='alert alert-success'>
            Job updated successfully.
        </div>";

        $query = mysqli_query(
            $conn,
            "SELECT *
             FROM jobs
             WHERE job_id='$job_id'"
        );

        $job = mysqli_fetch_assoc($query);

    }else{

        $message = "
        <div class='alert alert-danger'>
            Failed to update job.
        </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Edit Job | HireConnect</title>

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

                <div class="card-header bg-warning">

                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square"></i>
                        Edit Job
                    </h4>

                </div>

                <div class="card-body">

                    <?= $message ?>

                    <form method="POST"
                          enctype="multipart/form-data">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Job Title
                                </label>

                                <input
                                    type="text"
                                    name="title"
                                    class="form-control"
                                    value="<?= htmlspecialchars($job['title']) ?>"
                                    required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Category
                                </label>

                                <select
                                    name="category"
                                    class="form-select">

                                    <option>
                                        <?= $job['category'] ?>
                                    </option>

                                    <option>Information Technology</option>
                                    <option>Finance</option>
                                    <option>Engineering</option>
                                    <option>Marketing</option>
                                    <option>Healthcare</option>
                                    <option>Education</option>
                                    <option>Human Resource</option>

                                </select>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Employment Type
                                </label>

                                <select
                                    name="employment_type"
                                    class="form-select">

                                    <option>
                                        <?= $job['employment_type'] ?>
                                    </option>

                                    <option>Full-Time</option>
                                    <option>Part-Time</option>
                                    <option>Contract</option>
                                    <option>Internship</option>
                                    <option>Remote</option>

                                </select>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Location
                                </label>

                                <input
                                    type="text"
                                    name="location"
                                    class="form-control"
                                    value="<?= htmlspecialchars($job['location']) ?>">

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Salary
                                </label>

                                <input
                                    type="text"
                                    name="salary"
                                    class="form-control"
                                    value="<?= htmlspecialchars($job['salary']) ?>">

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Application Deadline
                                </label>

                                <input
                                    type="date"
                                    name="deadline"
                                    class="form-control"
                                    value="<?= $job['deadline'] ?>">

                            </div>

                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Current Logo
                                </label>

                                <br>

                                <?php if(!empty($job['logo'])){ ?>

                                    <img
                                    src="../uploads/logos/<?= $job['logo'] ?>"
                                    width="120"
                                    class="img-thumbnail">

                                <?php }else{ ?>

                                    <p>No logo uploaded.</p>

                                <?php } ?>

                            </div>

                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Replace Logo
                                </label>

                                <input
                                    type="file"
                                    name="company_logo"
                                    class="form-control">

                            </div>

                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Job Description
                                </label>

                                <textarea
                                    name="description"
                                    rows="6"
                                    class="form-control"><?= htmlspecialchars($job['description']) ?></textarea>
                                        <textarea id="description" name="description"></textarea>
                            </div>

                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Requirements
                                </label>

                                <textarea
                                    name="requirements"
                                    rows="5"
                                    class="form-control"><?= htmlspecialchars($job['requirements']) ?></textarea>

                            </div>

                        </div>

                        <button
                            type="submit"
                            name="update_job"
                            class="btn btn-success">

                            <i class="bi bi-check-circle"></i>
                            Update Job

                        </button>

                        <a href="manage_jobs.php"
                           class="btn btn-secondary">

                            Back

                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <?php include "includes/footer.php"; ?>

</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>
ClassicEditor.create(
    document.querySelector('#description')
);
</script>

</body>
</html>