<?php

require_once __DIR__ . "/auth.php";

/*
|--------------------------------------------------------------------------
| Employer Only
|--------------------------------------------------------------------------
*/

if ($_SESSION['role'] !== "employer") {

    header("Location: /odayos_works/hire_connect/login.php");
    exit();
}