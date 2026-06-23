<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <li><a href="register.php">Register</a></li>
        </ul>
    </nav>
</header>

<section class="register-section">

    <div class="register-container">

        <h2>Create Your Account</h2>
        <p>Join HireConnect and start your career journey today.</p>

        <form action="register_process.php" method="POST">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullname" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <label>Account Type</label>
                <select name="role" required>
                    <option value="">Select Account Type</option>
                    <option value="jobseeker">Job Seeker</option>
                    <option value="employer">Employer</option>
                </select>
            </div>

            <button type="submit" class="btn-register">
                Register
            </button>

        </form>

        <div class="login-link">
            Already have an account?
            <a href="login.php">Login Here</a>
        </div>

    </div>

</section>

<footer class="footer">
    <div class="footer-bottom">
        <p>&copy; 2025 HireConnect. All Rights Reserved.</p>
    </div>
</footer>

</body>
</html>