<?php

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $subject = htmlspecialchars($_POST["subject"]);

    $message = "Thank you, $name! Your message has been received. We will contact you soon.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | HireConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets\home_page.css">
    <style>
        body{
            background:#f8f9fa;
        }

        .hero{
            background:linear-gradient(135deg,#0d6efd,#4f8cff);
            color:white;
            padding:80px 0;
            text-align:center;
        }

        .contact-card{
            max-width:800px;
            margin:50px auto;
        }

        .info-box{
            background:white;
            padding:20px;
            border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,.1);
            height:100%;
        }
    </style>
</head>
<body>

    <header>
<a href="index.php" class="floating-home">
    🏠
</a>
</header>

<!-- Hero Section -->
<section class="hero">

    <div class="container">

        <h1 class="display-4 fw-bold">
            Contact HireConnect
        </h1>

        <p class="lead">
            We'd love to hear from you. Send us a message anytime.
        </p>

    </div>

</section>

<div class="container">

    <div class="row g-4 mt-4">

        <!-- Contact Information -->
        <div class="col-lg-4">

            <div class="info-box">

                <h4>Contact Information</h4>

                <p>
                    <strong>Email:</strong><br>
                    nicklasodayo@gmail.com
                </p>

                <p>
                    <strong>Phone:</strong><br>
                    +254 746867743
                </p>

                <p>
                    <strong>Location:</strong><br>
                    Nairobi, Kenya
                </p>

            </div>

        </div>

        <!-- Contact Form -->
        <div class="col-lg-8">

            <div class="card shadow">

                <div class="card-body">

                    <h3 class="mb-4">
                        Send a Message
                    </h3>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">

                            <label class="form-label">
                                Full Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Subject
                            </label>

                            <input
                                type="text"
                                name="subject"
                                class="form-control"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Message
                            </label>

                            <textarea
                                name="message"
                                rows="5"
                                class="form-control"
                                required
                            ></textarea>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary">
                            Send Message
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- Footer -->
<footer class="bg-dark text-light text-center py-4 mt-5">

    <div class="container">
        <p class="mb-0">
            © 2026 HireConnect. All Rights Reserved.
        </p>
    </div>

</footer>

</body>
</html>