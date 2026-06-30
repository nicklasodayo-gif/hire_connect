<?php

session_start();

require_once "../includes/employer_auth.php";
require_once "../config/config.php";

if(isset($_GET['id'])){

    $id = intval($_GET['id']);

    $stmt = $conn->prepare("
        DELETE FROM interviews
        WHERE interview_id=?
    ");

    $stmt->bind_param("i",$id);

    $stmt->execute();
}

header("Location: interview_calendar.php");
exit();