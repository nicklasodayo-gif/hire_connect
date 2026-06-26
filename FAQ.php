<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ | HireConnect</title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets\FAQ.css">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- ================= HEADER ================= -->

<header>

    <div class="logo">
        <a href="index.php">HireConnect</a>
    </div>

    <nav>

        <ul>

            <li><a href="index.php">Home</a></li>

            <li><a href="about.php">About</a></li>

            <li><a href="jobs.php">Jobs</a></li>

            <li><a href="faq.php" class="active">FAQ</a></li>

            <li><a href="contact.php">Contact</a></li>

        </ul>

    </nav>

    <div class="auth-buttons">

        <a href="login.php" class="login">Login</a>

        <a href="register.php" class="register">Register</a>

    </div>

</header>

<!-- ================= FAQ ================= -->

<section class="faq">

    <div class="faq-container">

        <h2>Frequently Asked Questions</h2>

        <p class="subtitle">
            Find answers to the most common questions about HireConnect.
        </p>

        <div class="faq-item">

            <button class="faq-btn">

                <span>How do I apply for a job?</span>

                <i class="fas fa-plus"></i>

            </button>

            <div class="faq-content">

                <p>
                    Register for a free account, browse available jobs,
                    open the job details page, and click the
                    <strong>Apply</strong> button.
                </p>

            </div>

        </div>

        <div class="faq-item">

            <button class="faq-btn">

                <span>Is registration free?</span>

                <i class="fas fa-plus"></i>

            </button>

            <div class="faq-content">

                <p>
                    Yes. Creating a Job Seeker account is completely free.
                </p>

            </div>

        </div>

        <div class="faq-item">

            <button class="faq-btn">

                <span>Can employers post jobs?</span>

                <i class="fas fa-plus"></i>

            </button>

            <div class="faq-content">

                <p>
                    Yes. Employers can create an employer account,
                    post jobs, manage applicants, and schedule interviews.
                </p>

            </div>

        </div>

        <div class="faq-item">

            <button class="faq-btn">

                <span>Can I upload my CV?</span>

                <i class="fas fa-plus"></i>

            </button>

            <div class="faq-content">

                <p>
                    Yes. Upload your CV from your Job Seeker dashboard
                    to make it available to employers.
                </p>

            </div>

        </div>

    </div>

</section>

<!-- ================= FOOTER ================= -->

<footer>

    <p>

        &copy; <?php echo date("Y"); ?>

        HireConnect. All Rights Reserved.

    </p>

</footer>

<!-- JavaScript -->
<script src="assets/js/faq.js"></script>

</body>
</html>