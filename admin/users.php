<?php
session_start();

require_once "../includes/admin_auth.php";
require_once "../config/config.php";
include "includes/header.php";
include "includes/sidebar.php";

/* ==========================================
   Flash Messages
========================================== */

if(isset($_SESSION['success'])){
    echo '
    <div class="alert alert-success alert-dismissible fade show">
        '.$_SESSION['success'].'
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
    unset($_SESSION['success']);
}

if(isset($_SESSION['error'])){
    echo '
    <div class="alert alert-danger alert-dismissible fade show">
        '.$_SESSION['error'].'
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
    unset($_SESSION['error']);
}
?>

<div class="container-fluid">

    <!-- Page Heading -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>

            <i class="bi bi-people-fill"></i>

            User Management

        </h2>

        <a href="add_user.php" class="btn btn-primary">

            <i class="bi bi-plus-circle"></i>

            Add User

        </a>

    </div>

    <!-- Filters -->

    <div class="card shadow mb-4">

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <input
                        type="text"
                        name="search"
                        id="search"
                        class="form-control"
                        placeholder="Search by name or email..."
                        onkeyup="loadUsers()">

                </div>

                <div class="col-md-3">

                    <select
                        name="role"
                        id="role"
                        class="form-select"
                        onchange="loadUsers()">

                        <option value="">All Roles</option>

                        <option value="admin">
                            Admin
                        </option>

                        <option value="employer">
                            Employer
                        </option>

                        <option value="jobseeker">
                            Job Seeker
                        </option>

                    </select>

                </div>

                <div class="col-md-3">

                    <button
                        class="btn btn-secondary w-100"
                        onclick="loadUsers()">

                        <i class="bi bi-arrow-repeat"></i>

                        Refresh

                    </button>

                </div>

            </div>

        </div>

    </div>

    <!-- AJAX Content -->

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                <i class="bi bi-table"></i>

                Users

            </h5>

        </div>

        <div class="card-body">

            <div id="loading" style="display:none;text-align:center;">

                <div class="spinner-border text-primary">

                </div>

                <p class="mt-2">

                    Loading users...

                </p>

            </div>

            <div class="mb-3">

    <button
        class="btn btn-danger"
        onclick="deleteSelectedUsers()">

        <i class="bi bi-trash"></i>

        Delete Selected

    </button>

    <button
        class="btn btn-secondary"
        onclick="loadUsers()">

        <i class="bi bi-arrow-clockwise"></i>

        Refresh

    </button>

</div>

            <div id="usersTable">

                <!-- AJAX loads here -->

            </div>

        </div>

    </div>

</div>

<script src="admin\user.js"></script>

<script>

window.onload=function(){

    loadUsers();

};

</script>

<?php include "includes/footer.php"; ?>