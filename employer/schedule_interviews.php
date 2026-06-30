<?php

require_once "../includes/employer_auth.php";
require_once "../config/config.php";
require_once "../mail/Mail.php";

$employer_id = $_SESSION['user_id'];

$message = "";
$messageType = "";

/* =====================================================
   FETCH SHORTLISTED APPLICANTS
===================================================== */

$stmt = $conn->prepare("
SELECT
    a.application_id,
    u.full_name,
    j.title
FROM applications a
JOIN users u
    ON u.user_id = a.applicant_id
JOIN jobs j
    ON j.job_id = a.job_id
WHERE
    a.status = 'Shortlisted'
    AND j.employer_id = ?
ORDER BY u.full_name
");

$stmt->bind_param("i", $employer_id);
$stmt->execute();

$applicants = $stmt->get_result();

/* =====================================================
   SCHEDULE INTERVIEW
===================================================== */

if (isset($_POST['schedule_interview'])) {

    $application_id = (int) $_POST['application_id'];
    $date = trim($_POST['interview_date']);
    $time = trim($_POST['interview_time']);
    $type = trim($_POST['interview_type']);
    $venue = trim($_POST['venue']);
    $notes = trim($_POST['notes']);

    if (empty($date) || empty($time)) {

        $message = "Interview date and time are required.";
        $messageType = "danger";

    } else {

        $conn->begin_transaction();

        try {

            /* ==============================
               Get Applicant Information
            ============================== */

            $stmt = $conn->prepare("
            SELECT
                u.full_name,
                u.email
            FROM applications a
            JOIN users u
                ON a.applicant_id = u.user_id
            WHERE a.application_id = ?
            ");

            $stmt->bind_param("i", $application_id);
            $stmt->execute();

            $applicant = $stmt->get_result()->fetch_assoc();

            /* ==============================
               Insert Interview
            ============================== */

            $stmt = $conn->prepare("
            INSERT INTO interviews(
                application_id,
                employer_id,
                interview_date,
                interview_time,
                interview_type,
                venue,
                notes
            )
            VALUES(?,?,?,?,?,?,?)
            ");

            $stmt->bind_param(
                "iisssss",
                $application_id,
                $employer_id,
                $date,
                $time,
                $type,
                $venue,
                $notes
            );

            $stmt->execute();

            /* ==============================
               Get Current Status
            ============================== */

            $stmt = $conn->prepare("
            SELECT status
            FROM applications
            WHERE application_id = ?
            ");

            $stmt->bind_param("i", $application_id);
            $stmt->execute();

            $oldStatus = $stmt->get_result()->fetch_assoc()['status'];

            /* ==============================
               Update Application Status
            ============================== */

            $newStatus = "Interview Scheduled";

            $stmt = $conn->prepare("
            UPDATE applications
            SET status = ?
            WHERE application_id = ?
            ");

            $stmt->bind_param(
                "si",
                $newStatus,
                $application_id
            );

            $stmt->execute();

            /* ==============================
               Save History
            ============================== */

            $remarks = "Interview scheduled for {$date} at {$time}";

            $stmt = $conn->prepare("
            INSERT INTO application_history(
                application_id,
                old_status,
                new_status,
                remarks
            )
            VALUES(?,?,?,?)
            ");

            $stmt->bind_param(
                "isss",
                $application_id,
                $oldStatus,
                $newStatus,
                $remarks
            );

            $stmt->execute();

            /* ==============================
               Send Email
            ============================== */

            $meetingLink = "";

            if (strtolower($type) == "online") {
                $meetingLink = $venue;
            }

            $emailSent = sendInterviewEmail(

                $applicant['email'],

                $applicant['full_name'],

                $date,

                $time,

                $type,

                $venue,

                $meetingLink

            );

            /* ==============================
               Commit Transaction
            ============================== */

            $conn->commit();

            if ($emailSent) {

                header("Location: interview_list.php?success=Interview scheduled successfully and email sent.");

            } else {

                header("Location: interview_list.php?success=Interview scheduled, but email could not be sent.");

            }

            exit();

        } catch (Exception $e) {

            $conn->rollback();

            $message = "Unable to schedule interview.";

            $messageType = "danger";

            // Uncomment while debugging:
            // $message = $e->getMessage();
        }
    }
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
href="employer\css\schedule_interview.css">

</head>

<body>

<div class="card shadow">

    <div class="card-header bg-primary text-white">

        <h4 class="mb-0">

            <i class="bi bi-calendar-check"></i>

            Schedule Interview

        </h4>

    </div>

    <div class="card-body">

        <?php if (!empty($message)) { ?>

            <div class="alert alert-<?= $messageType; ?>">

                <?= htmlspecialchars($message); ?>

            </div>

        <?php } ?>

        <form method="POST">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="form-label">

                        Applicant

                    </label>

                    <select
                        name="application_id"
                        class="form-select"
                        required>

                        <option value="">

                            Select Applicant

                        </option>

                        <?php while ($row = $applicants->fetch_assoc()) { ?>

                            <option value="<?= $row['application_id']; ?>">

                                <?= htmlspecialchars($row['full_name']); ?>

                                -

                                <?= htmlspecialchars($row['title']); ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="col-md-3 mb-3">

                    <label class="form-label">

                        Interview Date

                    </label>

                    <input
                        type="date"
                        name="interview_date"
                        class="form-control"
                        required
                        min="<?= date('Y-m-d'); ?>">

                </div>

                <div class="col-md-3 mb-3">

                    <label class="form-label">

                        Interview Time

                    </label>

                    <input
                        type="time"
                        name="interview_time"
                        class="form-control"
                        required>

                </div>

            </div>

            <div class="row">

                <div class="col-md-4 mb-3">

                    <label class="form-label">

                        Interview Type

                    </label>

                    <select
                        name="interview_type"
                        class="form-select">

                        <option value="Physical">

                            Physical

                        </option>

                        <option value="Online">

                            Online

                        </option>

                    </select>

                </div>

                <div class="col-md-8 mb-3">

                    <label class="form-label">

                        Venue / Meeting Link

                    </label>

                    <input
                        type="text"
                        name="venue"
                        class="form-control"
                        placeholder="Office address or Google Meet / Zoom link">

                </div>

            </div>

            <div class="mb-3">

                <label class="form-label">

                    Notes

                </label>

                <textarea
                    name="notes"
                    rows="5"
                    class="form-control"
                    placeholder="Additional interview instructions..."></textarea>

            </div>

            <button
                type="submit"
                name="schedule_interview"
                class="btn btn-success"
                onclick="return confirm('Schedule this interview?');">

                <i class="bi bi-calendar-check"></i>

                Schedule Interview

            </button>

            <a
                href="interview_list.php"
                class="btn btn-secondary">

                Cancel

            </a>

        </form>

    </div>

</div>