<?php
include "includes/auth.php";
include "../connect.php";

$employer_id = $_SESSION['user_id'];

$message = "";

/*
|--------------------------------------------------------------------------
| GET EMPLOYER DETAILS
|--------------------------------------------------------------------------
*/

$query = mysqli_query(
    $conn,
    "SELECT *
     FROM employers
     WHERE employer_id = '$employer_id'"
);

$employer = mysqli_fetch_assoc($query);

/*
|--------------------------------------------------------------------------
| UPDATE PROFILE
|--------------------------------------------------------------------------
*/

if(isset($_POST['update_profile'])){

    $company_name = mysqli_real_escape_string(
        $conn,
        $_POST['company_name']
    );

    $website = mysqli_real_escape_string(
        $conn,
        $_POST['website']
    );

    $industry = mysqli_real_escape_string(
        $conn,
        $_POST['industry']
    );

    $location = mysqli_real_escape_string(
        $conn,
        $_POST['location']
    );

    $description = mysqli_real_escape_string(
        $conn,
        $_POST['description']
    );

    $logo = $employer['logo'];

    /*
    |--------------------------------------------------------------------------
    | LOGO UPLOAD
    |--------------------------------------------------------------------------
    */

    if(!empty($_FILES['logo']['name'])){

        $logo =
            time() . "_" .
            basename($_FILES['logo']['name']);

        move_uploaded_file(
            $_FILES['logo']['tmp_name'],
            "../uploads/logos/" . $logo
        );
    }

    $update = mysqli_query(
        $conn,
        "UPDATE employers SET

            company_name='$company_name',
            website='$website',
            industry='$industry',
            location='$location',
            description='$description',
            logo='$logo'

        WHERE employer_id='$employer_id'"
    );

    if($update){

        $message = "
        <div class='alert alert-success'>
            <i class='bi bi-check-circle'></i>
            Company profile updated successfully.
        </div>";

        $query = mysqli_query(
            $conn,
            "SELECT *
             FROM employers
             WHERE employer_id='$employer_id'"
        );

        $employer = mysqli_fetch_assoc($query);

    }else{

        $message = "
        <div class='alert alert-danger'>
            Failed to update profile.
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

<title>Company Profile | HireConnect</title>

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

                    <h4 class="mb-0">
                        <i class="bi bi-building"></i>
                        Company Profile
                    </h4>

                </div>

                <div class="card-body">

                    <?= $message ?>

                    <form method="POST"
                          enctype="multipart/form-data">

                        <div class="row">

                            <!-- Company Logo -->

                            <div class="col-md-12 mb-4 text-center">

                                <?php if(!empty($employer['logo'])){ ?>

                                    <img
                                        src="../uploads/logos/<?= $employer['logo'] ?>"
                                        class="img-thumbnail"
                                        style="max-width:180px;">

                                <?php } else { ?>

                                    <div class="p-4 border rounded">

                                        <i class="bi bi-building"
                                           style="font-size:60px;"></i>

                                        <p class="mt-2">
                                            No Company Logo
                                        </p>

                                    </div>

                                <?php } ?>

                            </div>

                            <!-- Upload Logo -->

                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Company Logo
                                </label>

                                <input
                                    type="file"
                                    name="logo"
                                    class="form-control">

                            </div>

                            <!-- Company Name -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Company Name
                                </label>

                                <input
                                    type="text"
                                    name="company_name"
                                    class="form-control"
                                    value="<?= htmlspecialchars($employer['company_name'] ?? '') ?>"
                                    required>

                            </div>

                            <!-- Industry -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Industry
                                </label>

                                <input
                                    type="text"
                                    name="industry"
                                    class="form-control"
                                    value="<?= htmlspecialchars($employer['industry'] ?? '') ?>">

                            </div>

                            <!-- Website -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Website
                                </label>

                                <input
                                    type="url"
                                    name="website"
                                    class="form-control"
                                    value="<?= htmlspecialchars($employer['website'] ?? '') ?>"
                                    placeholder="https://example.com">

                            </div>

                            <!-- Location -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Location
                                </label>

                                <input
                                    type="text"
                                    name="location"
                                    class="form-control"
                                    value="<?= htmlspecialchars($employer['location'] ?? '') ?>">

                            </div>

                            <!-- Description -->

                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    About Company
                                </label>

                                <textarea
                                    name="description"
                                    rows="8"
                                    class="form-control"><?= htmlspecialchars($employer['description'] ?? '') ?></textarea>

                            </div>

                        </div>

                        <button
                            type="submit"
                            name="update_profile"
                            class="btn btn-success">

                            <i class="bi bi-save"></i>
                            Save Changes

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <?php include "includes/footer.php"; ?>

</div>

</body>
</html>