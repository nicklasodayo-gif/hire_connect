<?php

require_once __DIR__ . "/auth.php";

/*
|--------------------------------------------------------------------------
| Job Seeker Only
|--------------------------------------------------------------------------
*/

if ($_SESSION['role'] !== "jobseeker") {

    header("Location: /odayos_works/hire_connect/login.php");
    exit();
}