<?php

$page_css = "assets/login.css";

?>

<div class="login-section">

    <div class="form-container">

        <h2>Forgot Password</h2>

        <p class="subtitle">
            Enter your email address and we'll send you a password reset link.
        </p>

        <form action="send_reset.php" method="POST">

            <input
                type="email"
                name="email"
                placeholder="Enter your Email"
                required
            >

            <button type="submit">
                Send Reset Link
            </button>

        </form>

        <div class="register-link">
            <a href="login.php">Back to Login</a>
        </div>

    </div>

</div>