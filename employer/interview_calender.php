<?php
session_start();

require_once "../includes/employer_auth.php";
require_once "../config/config.php";
$page_title = "Interview Calendar";

/* Fetch Interviews */
$sql = $conn->query("
SELECT

interviews.interview_id,
interviews.interview_date,
interviews.interview_time,
interviews.status,

users.full_name,

jobs.title

FROM interviews

JOIN applications
ON interviews.application_id=applications.application_id

JOIN users
ON users.user_id=applications.user_id

JOIN jobs
ON jobs.job_id=applications.job_id

ORDER BY interview_date ASC
");

$events = [];

while($row = $sql->fetch_assoc()){

    switch($row['status']){

        case "Scheduled":
            $color="#0d6efd";
        break;

        case "Completed":
            $color="#198754";
        break;

        case "Cancelled":
            $color="#dc3545";
        break;

        default:
            $color="#6c757d";

    }

    $events[] = [

        "id"=>$row['interview_id'],

        "title"=>$row['full_name']." - ".$row['title'],

        "start"=>$row['interview_date']."T".$row['interview_time'],

        "color"=>$color

    ];

}

?>
<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>Interview Calendar</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css"
rel="stylesheet">

<style>

body{

background:#f5f7fb;

}

.card{

border:none;

border-radius:15px;

}

#calendar{

max-width:100%;

margin:auto;

}

.fc-toolbar-title{

font-size:22px;

font-weight:600;

}

.fc-event{

cursor:pointer;

border:none;

padding:3px;

}

.legend{

display:flex;

gap:20px;

margin-bottom:20px;

}

.legend span{

display:flex;

align-items:center;

}

.dot{

width:15px;

height:15px;

border-radius:50%;

display:inline-block;

margin-right:8px;

}

</style>

</head>

<body>

<div class="container-fluid">

<div class="row">

<!-- Sidebar -->

<div class="col-md-2 p-0">

<?php include "employer_sidebar.php"; ?>

</div>

<!-- Main Content -->

<div class="col-md-10">

<div class="container py-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>

<i class="bi bi-calendar-event"></i>

Interview Calendar

</h2>

<a
href="schedule_interviews.php"
class="btn btn-primary">

<i class="bi bi-plus-circle"></i>

Schedule Interview

</a>

</div>

<!-- Legend -->

<div class="legend">

<span>

<div
class="dot"
style="background:#0d6efd"></div>

Scheduled

</span>

<span>

<div
class="dot"
style="background:#198754"></div>

Completed

</span>

<span>

<div
class="dot"
style="background:#dc3545"></div>

Cancelled

</span>

</div>

<div class="card shadow">

<div class="card-body">

<div id="calendar"></div>

</div>

</div>

</div>

</div>

</div>

</div>

<script>

const interviewEvents = <?= json_encode($events); ?>;

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

let calendarEl = document.getElementById('calendar');

let calendar = new FullCalendar.Calendar(calendarEl, {

initialView:'dayGridMonth',

height:750,

headerToolbar:{

left:'prev,next today',

center:'title',

right:'dayGridMonth,timeGridWeek,timeGridDay'

},

events: interviewEvents,

editable:false,

navLinks:true,

nowIndicator:true,

eventTimeFormat:{

hour:'2-digit',

minute:'2-digit',

hour12:true

},

eventClick:function(info){

window.location="edit_interview.php?id="+info.event.id;

},

});

calendar.render();

});

</script>

</body>

</html>