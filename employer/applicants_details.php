<?php
require_once "../includes/employer_auth.php";
require_once "../config/config.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<div class='alert alert-danger'>Invalid application ID.</div>");
}

$id = (int) $_GET['id'];

// Fetch applicant using prepared statement
$stmt = $conn->prepare("
SELECT
    applications.*,
    users.full_name,
    users.email,
    users.phone,
    users.cv,
    jobs.title
FROM applications
JOIN users ON users.user_id = applications.user_id
JOIN jobs ON jobs.job_id = applications.job_id
WHERE application_id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();
$applicant = $stmt->get_result()->fetch_assoc();

// Check if application exists
if (!$applicant) {
    die("<div class='alert alert-danger'>Application not found.</div>");
}

// Status badge colors
$colors = [
    "Applied" => "secondary",
    "Under Review" => "warning",
    "Shortlisted" => "info",
    "Interview Scheduled" => "primary",
    "Hired" => "success",
    "Rejected" => "danger"
];

$class = $colors[$applicant['status']] ?? "dark";
?>

<!-- Applicant Information -->
<div class="card shadow-sm mb-4">

    <div class="card-header bg-primary text-white">
        Applicant Information
    </div>

    <div class="card-body">

        <h4><?= htmlspecialchars($applicant['full_name']); ?></h4>

        <p>
            <strong>Email:</strong>
            <?= htmlspecialchars($applicant['email']); ?>
        </p>

        <p>
            <strong>Phone:</strong>
            <?= htmlspecialchars($applicant['phone']); ?>
        </p>

        <p>
            <strong>Job:</strong>
            <?= htmlspecialchars($applicant['title']); ?>
        </p>

        <p>
            <strong>Applied:</strong>
            <?= date("d M Y", strtotime($applicant['application_date'])); ?>
        </p>

        <?php if(isset($applicant['remarks'])){ ?>
        <p>
            <strong>Current Remarks:</strong><br>
            <?= nl2br(htmlspecialchars($applicant['remarks'])); ?>
        </p>
        <?php } ?>

        <span class="badge bg-<?= $class; ?> fs-6">
            <?= htmlspecialchars($applicant['status']); ?>
        </span>

    </div>

</div>

<!-- Resume -->
<div class="card shadow-sm mb-4">

    <div class="card-header">
        Resume
    </div>

    <div class="card-body">

        <?php if(!empty($applicant['cv'])){ ?>

            <a href="../uploads/cv/<?= urlencode($applicant['cv']); ?>"
               class="btn btn-success"
               target="_blank">

                <i class="bi bi-download"></i>
                Download CV

            </a>

        <?php } else { ?>

            <div class="alert alert-warning">
                No CV uploaded.
            </div>

        <?php } ?>

    </div>

</div>

<!-- Update Status -->
<div class="card shadow">

    <div class="card-header">
        Update Application Status
    </div>

    <div class="card-body">

        <form action="update_application_status.php" method="POST">

            <input
                type="hidden"
                name="application_id"
                value="<?= $applicant['application_id']; ?>">

            <div class="mb-3">

                <label class="form-label">Status</label>

                <select name="status" class="form-select">

                    <?php
                    $statuses = [
                        "Applied",
                        "Under Review",
                        "Shortlisted",
                        "Interview Scheduled",
                        "Hired",
                        "Rejected"
                    ];

                    foreach($statuses as $status){
                    ?>

                        <option value="<?= $status; ?>"
                            <?= $status == $applicant['status'] ? "selected" : ""; ?>>

                            <?= $status; ?>

                        </option>

                    <?php } ?>

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">Remarks</label>

                <textarea
                    name="remarks"
                    class="form-control"
                    rows="4"></textarea>

            </div>

            <button
                type="submit"
                name="update_status"
                class="btn btn-primary"
                onclick="return confirm('Update this application status?');">

                <i class="bi bi-check-circle"></i>
                Update Status

            </button>

        </form>

    </div>

</div>

<?php
// Fetch application history
$stmt = $conn->prepare("
SELECT *
FROM application_history
WHERE application_id = ?
ORDER BY changed_at DESC
");

$stmt->bind_param("i", $id);
$stmt->execute();
$history = $stmt->get_result();
?>

<!-- History -->
<div class="card mt-4">

    <div class="card-header">
        Application History
    </div>

    <div class="card-body">

        <?php if($history->num_rows > 0){ ?>

        <table class="table table-bordered table-striped">

            <thead>

                <tr>
                    <th>Date</th>
                    <th>Old Status</th>
                    <th>New Status</th>
                    <th>Remarks</th>
                </tr>

            </thead>

            <tbody>

                <?php while($row = $history->fetch_assoc()){ ?>

                <tr>

                    <td>
                        <?= date("d M Y H:i", strtotime($row['changed_at'])); ?>
                    </td>

                    <td>
                        <span class="badge bg-<?= $colors[$row['old_status']] ?? 'dark'; ?>">
                            <?= htmlspecialchars($row['old_status']); ?>
                        </span>
                    </td>

                    <td>
                        <span class="badge bg-<?= $colors[$row['new_status']] ?? 'dark'; ?>">
                            <?= htmlspecialchars($row['new_status']); ?>
                        </span>
                    </td>

                    <td>
                        <?= !empty($row['remarks'])
                            ? nl2br(htmlspecialchars($row['remarks']))
                            : "-"; ?>
                    </td>

                </tr>

                <?php } ?>

            </tbody>

        </table>

        <?php } else { ?>

            <div class="alert alert-info">
                No status updates yet.
            </div>

        <?php } ?>

    </div>

</div>
```
