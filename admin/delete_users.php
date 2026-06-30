<?php
session_start();
require_once "../includes/admin_auth.php";
require_once "../config/config.php";

if (!isset($_POST['ids'])) {
    exit("No users selected.");
}

$ids = $_POST['ids'];

if (!is_array($ids) || count($ids) == 0) {
    exit("No users selected.");
}

$conn->begin_transaction();

try {

    foreach ($ids as $id) {

        $id = (int)$id;

        // Prevent deleting current admin
        if ($id == $_SESSION['user_id']) {
            continue;
        }

        // Get role
        $stmt = $conn->prepare("
            SELECT role
            FROM users
            WHERE user_id=?
        ");

        $stmt->bind_param("i",$id);
        $stmt->execute();

        $user = $stmt->get_result()->fetch_assoc();

        if(!$user){
            continue;
        }

        /* Delete applicant interviews */

        $stmt = $conn->prepare("
        DELETE i
        FROM interviews i
        INNER JOIN applications a
        ON i.application_id=a.application_id
        WHERE a.applicant_id=?
        ");

        $stmt->bind_param("i",$id);
        $stmt->execute();

        /* Delete application history */

        $stmt = $conn->prepare("
        DELETE
        FROM application_history
        WHERE application_id IN
        (
            SELECT application_id
            FROM applications
            WHERE applicant_id=?
        )
        ");

        $stmt->bind_param("i",$id);
        $stmt->execute();

        /* Delete applications */

        $stmt = $conn->prepare("
        DELETE
        FROM applications
        WHERE applicant_id=?
        ");

        $stmt->bind_param("i",$id);
        $stmt->execute();

        /* Employer */

        if($user['role']=="employer"){

            $stmt = $conn->prepare("
            DELETE
            FROM jobs
            WHERE employer_id=?
            ");

            $stmt->bind_param("i",$id);
            $stmt->execute();

        }

        /* Delete user */

        $stmt = $conn->prepare("
        DELETE
        FROM users
        WHERE user_id=?
        ");

        $stmt->bind_param("i",$id);
        $stmt->execute();

    }

    $conn->commit();

    echo "success";

}catch(Exception $e){

    $conn->rollback();

    echo $e->getMessage();

}
?>