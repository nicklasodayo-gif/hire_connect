<?php
require_once "../includes/employer_auth.php";
require_once "../config/config.php";

$employer_id = $_SESSION['user_id'];

$message = "";

/*
|--------------------------------------------------------------------------
| GET EMPLOYER DATA
|--------------------------------------------------------------------------
*/

$query = mysqli_query(
    $conn,
    "SELECT *
     FROM employers
     WHERE employer_id='$employer_id'"
);

$employer = mysqli_fetch_assoc($query);

/*
|--------------------------------------------------------------------------
| UPDATE EMAIL & NOTIFICATIONS
|--------------------------------------------------------------------------
*/

if(isset($_POST['save_settings'])){

    $email = mysqli_real_escape_string(
        $conn,
        trim($_POST['email'])
    );

    $notifications = mysqli_real_escape_string(
        $conn,
        $_POST['notifications']
    );

    $update = mysqli_query(
        $conn,
        "UPDATE employers
         SET
         email='$email',
         notifications='$notifications'
         WHERE employer_id='$employer_id'"
    );

    if($update){

        $message = "
        <div class='alert alert-success'>
            Settings updated successfully.
        </div>";

        $query = mysqli_query(
            $conn,
            "SELECT *
             FROM employers
             WHERE employer_id='$employer_id'"
        );

        $employer = mysqli_fetch_assoc($query);
    }
}

/*
|--------------------------------------------------------------------------
| CHANGE PASSWORD
|--------------------------------------------------------------------------
*/

if(isset($_POST['change_password'])){

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if(!password_verify(
        $current_password,
        $employer['password']
    )){

        $message = "
        <div class='alert alert-danger'>
            Current password is incorrect.
        </div>";

    }elseif($new_password !== $confirm_password){

        $message = "
        <div class='alert alert-danger'>
            New passwords do not match.
        </div>";

    }else{

        $hashed_password = password_hash(
            $new_password,
            PASSWORD_DEFAULT
        );

        mysqli_query(
            $conn,
            "UPDATE employers
             SET password='$hashed_password'
             WHERE employer_id='$employer_id'"
        );

        $message = "
        <div class='alert alert-success'>
            Password changed successfully.
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

<title>Settings | HireConnect</title>

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

            <?= $message ?>

            <!-- Account Settings -->

            <div class="card shadow-sm mb-4">

                <div class="card-header bg-primary text-white">

                    <h4 class="mb-0">
                        <i class="bi bi-gear-fill"></i>
                        Account Settings
                    </h4>

                </div>

                <div class="card-body">

                    <form method="POST">

                        <div class="mb-3">

                            <label class="form-label">
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?= htmlspecialchars($employer['email']) ?>"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Receive Notifications
                            </label>

                            <select
                                name="notifications"
                                class="form-select">

                                <option value="Yes"
                                <?= ($employer['notifications'] ?? 'Yes') == 'Yes' ? 'selected' : '' ?>>
                                    Yes
                                </option>

                                <option value="No"
                                <?= ($employer['notifications'] ?? 'Yes') == 'No' ? 'selected' : '' ?>>
                                    No
                                </option>

                            </select>

                        </div>

                        <button
                            type="submit"
                            name="save_settings"
                            class="btn btn-primary">

                            <i class="bi bi-save"></i>
                            Save Settings

                        </button>

                    </form>

                </div>

            </div>

            <!-- Change Password -->

            <div class="card shadow-sm">

                <div class="card-header bg-warning">

                    <h4 class="mb-0">
                        <i class="bi bi-shield-lock"></i>
                        Change Password
                    </h4>

                </div>

                <div class="card-body">

                    <form method="POST">

                        <div class="mb-3">

                            <label class="form-label">
                                Current Password
                            </label>

                            <input
                                type="password"
                                name="current_password"
                                class="form-control"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                New Password
                            </label>

                            <input
                                type="password"
                                name="new_password"
                                class="form-control"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Confirm Password
                            </label>

                            <input
                                type="password"
                                name="confirm_password"
                                class="form-control"
                                required>

                        </div>

                        <button
                            type="submit"
                            name="change_password"
                            class="btn btn-success">

                            <i class="bi bi-key"></i>
                            Update Password

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