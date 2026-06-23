<?php

$page_css = "assets/login.css";
include 'header.php';

?>

<div class="login-section">

    <div class="form-container">

        <h2>Welcome Back</h2>
        <p class="subtitle">
            Sign in to access your HireConnect account
        </p>

        <?php if(isset($error)): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">

            <input
                type="email"
                name="email"
                placeholder="Email Address"
                required
            >

            <input
                type="password"
                name="password"
                placeholder="Password"
                required
            >

            <div class="form-options">

                <label class="remember-me">
                    <input type="checkbox" name="remember">
                    Remember Me
                </label>

                <a href="assets\forgotten_Password.php" class="forgot-link">
                    Forgot Password?
                </a>

            </div>

            <button type="submit">
                Login
            </button>

        </form>

        <div class="register-link">
            Don't have an account?
            <a href="register.php">Register Here</a>
        </div>

    </div>

</div>

<?php include 'footer.php'; ?>