<?php
session_start();
include("connect.php");

$message = "";

if (isset($_POST['submit'])) {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Validation
    if (empty($full_name) || empty($email) || empty($phone) || empty($password) || empty($role)) {

        $message = "Please fill in all required fields.";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";

    } else {

        // Check if email already exists
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "Email address already exists.";

        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO users
                (full_name, email, phone, password, role)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "sssss",
                $full_name,
                $email,
                $phone,
                $hashed_password,
                $role
            );

            if ($stmt->execute()) {

                $message = "Registration successful! You can now login.";

            } else {

                $message = "Registration failed: " . $stmt->error;

            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Register | HireConnect</title>

    <link rel="stylesheet" href="assets/register.css">

</head>
<body>

<header>

    <div class="logo">
        <a href="index.php">HireConnect</a>
    </div>

    <nav>

        <ul>

            <li><a href="index.php">Home</a></li>

            <li><a href="about.php">About Us</a></li>

            <li><a href="jobs.php">Job Listings</a></li>

            <li><a href="contact.php">Contact Us</a></li>

            <li><a href="login.php">Login</a></li>

            <li><a href="register.php" class="active">Register</a></li>

        </ul>

    </nav>

</header>

<section class="register-section">

    <div class="register-container">

        <h2>Create Your Account</h2>

        <p>
            Join HireConnect and start your career journey today.
        </p>

        <?php if (!empty($message)) { ?>

            <div class="alert">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php } ?>

        <form action="register.php" method="POST">

            <div class="form-group">

                <label>Full Name</label>

                <input
                    type="text"
                    name="full_name"
                    required>

            </div>

            <div class="form-group">

                <label>Email Address</label>

                <input
                    type="email"
                    name="email"
                    required>

            </div>

            <div class="form-group">

                <label>Phone Number</label>

                <input
                    type="tel"
                    name="phone"
                    required>

            </div>

            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    required>

            </div>

            <div class="form-group">

                <label>Confirm Password</label>

                <input
                    type="password"
                    name="confirm_password"
                    required>

            </div>

            <div class="form-group">

                <label>Account Type</label>

                <select
                    name="role"
                    required>

                    <option value="">Select Account Type</option>

                    <option value="jobseeker">
                        Job Seeker
                    </option>

                    <option value="employer">
                        Employer
                    </option>

                </select>

            </div>

            <button
                type="submit"
                name="submit"
                class="btn-register">

                Register

            </button>

        </form>

        <div class="login-link">

            Already have an account?

            <a href="login.php">
                Login Here
            </a>

        </div>

    </div>

</section>

<footer class="footer">

    <div class="footer-bottom">

        <p>

            &copy; <?php echo date("Y"); ?>

            HireConnect. All Rights Reserved.

        </p>

    </div>

</footer>

</body>
</html>