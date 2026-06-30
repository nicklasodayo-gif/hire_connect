<?php

include "../connect.php";

$sql = "
SELECT
    interviews.*,
    users.full_name,
    jobs.title
FROM interviews
JOIN applications
    ON applications.application_id = interviews.application_id
JOIN users
    ON users.user_id = applications.applicant_id
JOIN jobs
    ON jobs.job_id = applications.job_id
ORDER BY
    interviews.interview_date ASC,
    interviews.interview_time ASC
";

$result = $conn->query($sql);

?>

<table class="table table-hover">

<thead>

<tr>

<th>Applicant</th>
<th>Job</th>
<th>Date</th>
<th>Time</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>

<td><?= htmlspecialchars($row['full_name']) ?></td>

<td><?= htmlspecialchars($row['title']) ?></td>

<td><?= date("d M Y", strtotime($row['interview_date'])) ?></td>

<td><?= date("g:i A", strtotime($row['interview_time'])) ?></td>

<td>

<?php

$status = strtolower($row['status']);

if($status == "scheduled"){

    echo '<span class="badge bg-primary">Scheduled</span>';

}elseif($status == "completed"){

    echo '<span class="badge bg-success">Completed</span>';

}elseif($status == "cancelled"){

    echo '<span class="badge bg-danger">Cancelled</span>';

}else{

    echo '<span class="badge bg-secondary">'
        . htmlspecialchars($row['status']) .
        '</span>';

}

?>

</td>

</tr>

<?php } ?>

</tbody>

</table>