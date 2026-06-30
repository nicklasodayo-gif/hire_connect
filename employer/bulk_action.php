<?php
session_start();

require_once "../config/config.php";
require_once "../includes/employer_auth.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Get Employer ID
|--------------------------------------------------------------------------
*/

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT employer_id
    FROM employers
    WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Employer profile not found.");
}

$employer = $result->fetch_assoc();
$employer_id = $employer['employer_id'];

$stmt->close();

/*
|--------------------------------------------------------------------------
| Validate Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("Location: manage_jobs.php");
    exit();
}

if (
    !isset($_POST['job_ids']) ||
    !is_array($_POST['job_ids']) ||
    empty($_POST['job_ids'])
) {

    $_SESSION['message'] =
        "<div class='alert alert-warning'>
            Please select at least one job.
        </div>";

    header("Location: manage_jobs.php");
    exit();
}

$action = $_POST['action'];
$job_ids = array_map('intval', $_POST['job_ids']);

/*
|--------------------------------------------------------------------------
| Execute Bulk Action
|--------------------------------------------------------------------------
*/

switch ($action) {

    case "delete":

        $stmt = $conn->prepare("
            DELETE FROM jobs
            WHERE job_id = ?
            AND employer_id = ?
        ");

        foreach ($job_ids as $id) {

            $stmt->bind_param(
                "ii",
                $id,
                $employer_id
            );

            $stmt->execute();
        }

        $_SESSION['message'] =
            "<div class='alert alert-success'>
                Selected jobs deleted successfully.
            </div>";

        break;

    case "open":

        $stmt = $conn->prepare("
            UPDATE jobs
            SET status='Open'
            WHERE job_id=?
            AND employer_id=?
        ");

        foreach ($job_ids as $id) {

            $stmt->bind_param(
                "ii",
                $id,
                $employer_id
            );

            $stmt->execute();
        }

        $_SESSION['message'] =
            "<div class='alert alert-success'>
                Selected jobs are now Open.
            </div>";

        break;

    case "close":

        $stmt = $conn->prepare("
            UPDATE jobs
            SET status='Closed'
            WHERE job_id=?
            AND employer_id=?
        ");

        foreach ($job_ids as $id) {

            $stmt->bind_param(
                "ii",
                $id,
                $employer_id
            );

            $stmt->execute();
        }

        $_SESSION['message'] =
            "<div class='alert alert-success'>
                Selected jobs are now Closed.
            </div>";

        break;

    case "pending":

        $stmt = $conn->prepare("
            UPDATE jobs
            SET approval_status='Pending'
            WHERE job_id=?
            AND employer_id=?
        ");

        foreach ($job_ids as $id) {

            $stmt->bind_param(
                "ii",
                $id,
                $employer_id
            );

            $stmt->execute();
        }

        $_SESSION['message'] =
            "<div class='alert alert-success'>
                Selected jobs submitted for approval.
            </div>";

        break;

    default:

        $_SESSION['message'] =
            "<div class='alert alert-danger'>
                Invalid bulk action.
            </div>";

        break;
}

header("Location: manage_jobs.php");
exit();