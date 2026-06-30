<?php
session_start();
require_once "../includes/employer_auth.php";
require_once "../config/config.php";

if(isset($_POST['update_status'])){

    $application_id = intval($_POST['application_id']);
    $new_status     = $_POST['status'];
    $remarks        = trim($_POST['remarks']);
    $changed_by     = $_SESSION['user_id'];

    // Get current status
    $stmt = $conn->prepare("SELECT status FROM applications WHERE application_id=?");
    $stmt->bind_param("i",$application_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $application = $result->fetch_assoc();

    $old_status = $application['status'];

    // Update application
    $update = $conn->prepare("
        UPDATE applications
        SET status=?
        WHERE application_id=?
    ");

    $update->bind_param("si",$new_status,$application_id);
    $update->execute();

    // Save history
    $history = $conn->prepare("
        INSERT INTO application_history
        (
            application_id,
            old_status,
            new_status,
            changed_by,
            remarks
        )
        VALUES(?,?,?,?,?)
    ");

    $history->bind_param(
        "issis",
        $application_id,
        $old_status,
        $new_status,
        $changed_by,
        $remarks
    );

    $history->execute();

    header("Location: applicant_details.php?id=".$application_id);
    exit();
}
?>