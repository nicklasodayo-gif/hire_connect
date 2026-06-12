<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HireConnect - Find Your Dream Job</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/home.css">
</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">

        <a class="navbar-brand fw-bold text-primary" href="index.php">
            HireConnect
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">

            <ul class="navbar-nav align-items-center gap-lg-3">

                <li class="nav-item">
                    <a class="nav-link" href="jobs.php">
                        Jobs
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="jobs.php">
                        Companies
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="career_advice.php">
                        Career Advice
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="candidates.php">
                        Search Candidates
                    </a>
                </li>

                <?php if (isset($_SESSION["user_id"])): ?>

                    <li class="nav-item">
                        <span class="nav-link">
                            Welcome,
                            <?php echo htmlspecialchars($_SESSION["fullname"]); ?>
                        </span>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-danger" href="logout.php">
                            Logout
                        </a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="btn btn-outline-primary" href="login.php">
                            Log In
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-success" href="register.php">
                            Create Account
                        </a>
                    </li>

                <?php endif; ?>

                <li class="nav-item">
                    <a class="btn btn-primary" href="post_job.php">
                        Post a Job
                    </a>
                </li>

            </ul>

        </div>

    </div>
</nav>

<!-- Hero Section -->
<section class="hero py-5">

    <div class="container">

        <div class="row align-items-center g-5">

            <div class="col-lg-6">

                <span class="hero-badge">
                    🚀 Find Your Dream Job Today
                </span>

                <h1 class="display-4 fw-bold mt-3">
                    Connect With Top Employers &
                    Discover
                    <span class="text-primary">
                        Your Next Career Opportunity
                    </span>
                </h1>

                <p class="lead text-muted my-4">
                    Search thousands of verified job listings,
                    internships, and remote opportunities
                    from leading companies.
                </p>

                <form action="jobs.php" method="GET" class="row g-2">

                    <div class="col-md-5">
                        <input
                            type="text"
                            name="keyword"
                            class="form-control form-control-lg"
                            placeholder="Job title, keywords, or company"
                        >
                    </div>

                    <div class="col-md-4">
                        <input
                            type="text"
                            name="location"
                            class="form-control form-control-lg"
                            placeholder="Location"
                        >
                    </div>

                    <div class="col-md-3">
                        <button
                            type="submit"
                            class="btn btn-primary btn-lg w-100">
                            Search Jobs
                        </button>
                    </div>

                </form>

                <div class="row text-center mt-5">

                    <div class="col-4">
                        <h3 class="fw-bold">50K+</h3>
                        <p class="text-muted">Active Jobs</p>
                    </div>

                    <div class="col-4">
                        <h3 class="fw-bold">10K+</h3>
                        <p class="text-muted">Companies</p>
                    </div>

                    <div class="col-4">
                        <h3 class="fw-bold">1M+</h3>
                        <p class="text-muted">Job Seekers</p>
                    </div>

                </div>

            </div>

            <div class="col-lg-6 text-center">

                <img
                    src="assets\images\meeting.jpg"
                    alt="People working together"
                    class="img-fluid rounded-4 shadow"
                >

            </div>

        </div>

    </div>

</section>

<!-- Footer -->
<footer class="footer bg-dark text-light pt-5">

    <div class="container">

        <div class="row gy-4">

            <div class="col-md-3">

                <h5>Job Seekers</h5>

                <ul class="list-unstyled">
                    <li><a href="jobs.php">Browse Jobs</a></li>
                    <li><a href="jobs.php">Remote Jobs</a></li>
                    <li><a href="jobs.php">Internships</a></li>
                </ul>

            </div>

            <div class="col-md-3">

                <h5>Employers</h5>

                <ul class="list-unstyled">
                    <li><a href="post_job.php">Post a Job</a></li>
                    <li><a href="candidates.php">Search Candidates</a></li>
                </ul>

            </div>

            <div class="col-md-3">

                <h5>Resources</h5>

                <ul class="list-unstyled">
                    <li><a href="career_advice.php">Career Advice</a></li>
                    <li><a href="inerview_prep.php">Interview Prep</a></li>
                </ul>

            </div>

            <div class="col-md-3">

                <h5>Company</h5>

                <ul class="list-unstyled">
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact..php">Contact</a></li>
                </ul>

            </div>

        </div>

        <hr class="my-4">

        <div class="text-center">
            <p>© 2026 HireConnect. All rights reserved.</p>
        </div>

    </div>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>