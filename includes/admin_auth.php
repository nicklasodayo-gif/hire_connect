<?php

require_once __DIR__ . "/auth.php";

/*
|--------------------------------------------------------------------------
| Admin Only
|--------------------------------------------------------------------------
*/

if ($_SESSION['role'] !== "admin") {

    header("Location: /odayos_works/hire_connect/login.php");
    exit();
}