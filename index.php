<?php
session_start();
include("connect.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>HireConnect | Find Your Dream Job</title>

    <link rel="stylesheet" href="assets/home.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

</head>

<body>

<!-- ================= HEADER ================= -->

<header>

    <div class="logo">

        <a href="index.php">

            HireConnect

        </a>

    </div>

    <nav>

        <ul>

            <li>
                <a href="index.php" class="active">
                    Home
                </a>
            </li>

            <li>
                <a href="about.php">
                    About
                </a>
            </li>

            <li>
                <a href="jobs.php">
                    Jobs
                </a>
            </li>

            <li>
                <a href="faq.php">
                    FAQ
                </a>
            </li>

            <li>
                <a href="contact.php">
                    Contact
                </a>
            </li>

        </ul>

    </nav>

    <div class="auth-buttons">

<?php

if(isset($_SESSION['user_id'])){

    if($_SESSION['role']=="jobseeker"){

        ?>

        <a href="jobseeker/dashboard.php"
           class="dashboard-btn">

            Dashboard

        </a>

        <?php

    }

    elseif($_SESSION['role']=="employer"){

        ?>

        <a href="employer/dashboard.php"
           class="dashboard-btn">

            Employer Dashboard

        </a>

        <?php

    }

    elseif($_SESSION['role']=="admin"){

        ?>

        <a href="admin/dashboard.php"
           class="dashboard-btn">

            Admin Panel

        </a>

        <?php

    }

?>

<a href="logout.php"
class="logout-btn">

Logout

</a>

<?php

}else{

?>

<a href="login.php"
class="login">

Login

</a>

<a href="register.php"
class="register">

Register

</a>

<?php

}

?>

    </div>

</header>

<!-- ================= HERO ================= -->

<section class="hero">

<div class="hero-text">

<div class="badge">

<i class="fas fa-briefcase"></i>

Your Career Starts Here

</div>

<h1>

Find Your

<span>

Dream Job

</span>

With HireConnect

</h1>

<p>

Connect with thousands of employers,

discover exciting career opportunities,

and take the next step in your professional journey.

</p>

<div class="hero-buttons">

<a href="jobs.php"
class="browse">

Browse Jobs

<i class="fas fa-arrow-right"></i>

</a>

<?php

if(isset($_SESSION['user_id'])){

if($_SESSION['role']=="jobseeker"){

?>

<a href="jobseeker/dashboard.php"
class="started">

Go To Dashboard

</a>

<?php

}

elseif($_SESSION['role']=="employer"){

?>

<a href="employer/dashboard.php"
class="started">

Employer Dashboard

</a>

<?php

}

else{

?>

<a href="admin/dashboard.php"
class="started">

Admin Dashboard

</a>

<?php

}

}else{

?>

<a href="register.php"
class="started">

Create Account

</a>

<?php

}

?>

</div>

</div>

<div class="hero-image">

<img
src="assets/images/OIP.webp"
alt="Career">

<div class="floating-card card1">

<h4>

10,000+

</h4>

<p>

Active Jobs

</p>

</div>

<div class="floating-card card2">

<h4>

5,000+

</h4>

<p>

Employers

</p>

</div>

<div class="floating-card card3">

<h4>

25,000+

</h4>

<p>

Successful Hires

</p>

</div>

</div>

</section>

<!-- ================= STATISTICS ================= -->

<section class="statistics">

    <h2>HireConnect in Numbers</h2>

    <div class="stats-container">

        <?php

        $jobs = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT COUNT(*) AS total
        FROM jobs
        WHERE status='Open'
        "));

        $employers = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT COUNT(*) AS total
        FROM users
        WHERE role='employer'
        "));

        $jobseekers = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT COUNT(*) AS total
        FROM users
        WHERE role='jobseeker'
        "));

        $applications = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT COUNT(*) AS total
        FROM applications
        "));

        ?>

        <div class="stat-card">

            <i class="fas fa-briefcase"></i>

            <h2><?= $jobs['total']; ?></h2>

            <p>Active Jobs</p>

        </div>

        <div class="stat-card">

            <i class="fas fa-building"></i>

            <h2><?= $employers['total']; ?></h2>

            <p>Employers</p>

        </div>

        <div class="stat-card">

            <i class="fas fa-users"></i>

            <h2><?= $jobseekers['total']; ?></h2>

            <p>Job Seekers</p>

        </div>

        <div class="stat-card">

            <i class="fas fa-file-signature"></i>

            <h2><?= $applications['total']; ?></h2>

            <p>Applications</p>

        </div>

    </div>

</section>

<section class="featured-jobs">

<h2>

Latest Job Opportunities

</h2>

<p>

Discover the newest opportunities from top employers.

</p>

<div class="job-grid">

<?php

$query = mysqli_query($conn,"
SELECT *
FROM jobs
WHERE status='Open'
ORDER BY created_at DESC
LIMIT 6
");

if(mysqli_num_rows($query)>0){

while($job=mysqli_fetch_assoc($query)){

$logo="uploads/logos/default.png";

if(!empty($job['logo'])){

$logo="uploads/logos/".$job['logo'];

}

?>

<div class="job-card">

<img
src="<?= $logo; ?>"
alt="Company Logo">

<h3>

<?= htmlspecialchars($job['title']); ?>

</h3>

<p>

<i class="fas fa-map-marker-alt"></i>

<?= htmlspecialchars($job['location']); ?>

</p>

<p>

<i class="fas fa-clock"></i>

<?= htmlspecialchars($job['employment_type']); ?>

</p>

<p>

<i class="fas fa-money-bill-wave"></i>

<?= htmlspecialchars($job['salary']); ?>

</p>

<a
href="jobseeker/job_details.php?id=<?= $job['job_id']; ?>"
class="view-job">

View Details

</a>

</div>

<?php

}

}else{

?>

<p>

No jobs available.

</p>

<?php

}

?>

</div>

</section>

<section class="categories">

<h2>

Popular Job Categories

</h2>

<div class="category-grid">

<a href="jobs.php?category=IT">

<i class="fas fa-laptop-code"></i>

Information Technology

</a>

<a href="jobs.php?category=Engineering">

<i class="fas fa-cogs"></i>

Engineering

</a>

<a href="jobs.php?category=Finance">

<i class="fas fa-chart-line"></i>

Finance

</a>

<a href="jobs.php?category=Healthcare">

<i class="fas fa-heartbeat"></i>

Healthcare

</a>

<a href="jobs.php?category=Education">

<i class="fas fa-graduation-cap"></i>

Education

</a>

<a href="jobs.php?category=Marketing">

<i class="fas fa-bullhorn"></i>

Marketing

</a>

</div>

</section>

<section class="how-it-works">

<h2>

How HireConnect Works

</h2>

<div class="steps">

<div class="step">

<i class="fas fa-user-plus"></i>

<h3>

Create an Account

</h3>

<p>

Register as a Job Seeker or Employer.

</p>

</div>

<div class="step">

<i class="fas fa-search"></i>

<h3>

Search Jobs

</h3>

<p>

Browse thousands of verified opportunities.

</p>

</div>

<div class="step">

<i class="fas fa-paper-plane"></i>

<h3>

Apply

</h3>

<p>

Upload your CV and apply instantly.

</p>

</div>

<div class="step">

<i class="fas fa-handshake"></i>

<h3>

Get Hired

</h3>

<p>

Track applications and land your dream job.

</p>

</div>

</div>

</section>

<section class="how-it-works">

<h2>

How HireConnect Works

</h2>

<div class="steps">

<div class="step">

<i class="fas fa-user-plus"></i>

<h3>

Create an Account

</h3>

<p>

Register as a Job Seeker or Employer.

</p>

</div>

<div class="step">

<i class="fas fa-search"></i>

<h3>

Search Jobs

</h3>

<p>

Browse thousands of verified opportunities.

</p>

</div>

<div class="step">

<i class="fas fa-paper-plane"></i>

<h3>

Apply

</h3>

<p>

Upload your CV and apply instantly.

</p>

</div>

<div class="step">

<i class="fas fa-handshake"></i>

<h3>

Get Hired

</h3>

<p>

Track applications and land your dream job.

</p>

</div>

</div>

</section>

<!-- ================= TRUSTED COMPANIES ================= -->

<section class="companies">

<h2>

Trusted by Leading Employers

</h2>

<div class="company-grid">

<div class="company-card">

<i class="fab fa-google fa-3x"></i>

<h4>Google</h4>

</div>

<div class="company-card">

<i class="fab fa-microsoft fa-3x"></i>

<h4>Microsoft</h4>

</div>

<div class="company-card">

<i class="fab fa-amazon fa-3x"></i>

<h4>Amazon</h4>

</div>

<div class="company-card">

<i class="fab fa-apple fa-3x"></i>

<h4>Apple</h4>

</div>

<div class="company-card">

<i class="fab fa-meta fa-3x"></i>

<h4>Meta</h4>

</div>

</div>

</section>

<!-- ================= TESTIMONIALS ================= -->

<section class="testimonials">

<h2>

Success Stories

</h2>

<div class="testimonial-container">

<div class="testimonial">

<img src="assets/images/user1.jpg">

<h3>Jane Smith</h3>

<h5>Software Engineer</h5>

<p>

"HireConnect helped me secure my dream job
within two weeks."

</p>

★★★★★

</div>

<div class="testimonial">

<img src="assets/images/user2.jpg">

<h3>John Carter</h3>

<h5>HR Manager</h5>

<p>

"We hired five talented developers using
HireConnect."

</p>

★★★★★

</div>

<div class="testimonial">

<img src="assets/images/user3.jpg">

<h3>Emily Brown</h3>

<h5>Graduate</h5>

<p>

"The dashboard made tracking my applications
extremely easy."

</p>

★★★★★

</div>

</div>

</section>

<!-- ================= NEWSLETTER ================= -->

<section class="newsletter">

<h2>

Stay Updated

</h2>

<p>

Receive the latest job opportunities directly in your inbox.

</p>

<form action="#" method="POST">

<input

type="email"

placeholder="Enter your email"

required>

<button>

Subscribe

</button>

</form>

</section>

<!-- ================= CTA ================= -->

<section class="cta">

<h2>

Ready to Start Your Career?

</h2>

<p>

Join thousands of employers and professionals using HireConnect.

</p>

<a href="register.php">

Create Your Free Account

</a>

</section>

<!-- ================= FOOTER ================= -->

<footer>

<div class="footer-grid">

<div>

<h3>

HireConnect

</h3>

<p>

Connecting talent with opportunity.

</p>

<div class="socials">

<i class="fab fa-facebook"></i>

<i class="fab fa-linkedin"></i>

<i class="fab fa-twitter"></i>

<i class="fab fa-instagram"></i>

</div>

</div>

<div>

<h4>

Quick Links

</h4>

<ul>

<li><a href="index.php">Home</a></li>

<li><a href="about.php">About</a></li>

<li><a href="jobs.php">Jobs</a></li>

<li><a href="contact.php">Contact</a></li>

</ul>

</div>

<div>

<h4>

Job Seekers

</h4>

<ul>

<li><a href="register.php">Register</a></li>

<li><a href="login.php">Login</a></li>

<li><a href="jobs.php">Browse Jobs</a></li>

<li><a href="jobseeker/dashboard.php">Dashboard</a></li>

</ul>

</div>

<div>

<h4>

Employers

</h4>

<ul>

<li><a href="register.php">Register Company</a></li>

<li><a href="employer\post_job.php">Post a Job</a></li>

<li><a href="employer\employer_dashboard.php">Employer Dashboard</a></li>

<li><a href="contact.php">Support</a></li>

</ul>

</div>

</div>

<hr>

<p class="copyright">

© <?php echo date("Y"); ?> HireConnect. All Rights Reserved.

</p>

</footer>

<button id="topBtn">

<i class="fas fa-arrow-up"></i>

</button>

<script>

const topBtn=document.getElementById("topBtn");

window.onscroll=function(){

if(document.body.scrollTop>200||

document.documentElement.scrollTop>200){

topBtn.style.display="block";

}else{

topBtn.style.display="none";

}

}

topBtn.onclick=function(){

window.scrollTo({

top:0,

behavior:"smooth"

});

}

</script>

</body>

</html>