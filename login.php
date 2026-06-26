<?php
session_start();
include 'connect.php';

$page_css = "assets/login.css";
include 'header.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT user_id, full_name, email, password, role FROM users WHERE email = ? LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            // SESSION
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // REMEMBER ME (FIXED)
            if (isset($_POST['remember'])) {

                $token = bin2hex(random_bytes(32));

                $stmt2 = $conn->prepare("
                    UPDATE users
                    SET remember_token = ?
                    WHERE user_id = ?
                ");

                $stmt2->bind_param("si", $token, $user['user_id']);
                $stmt2->execute();

                setcookie(
                    "remember_token",
                    $token,
                    time() + (86400 * 30),
                    "/",
                    "",
                    false,
                    true
                );
            }

            // ROLE REDIRECT (FIXED PATHS)
            if ($user['role'] === 'employer') {
                header("Location: employer/employer_dashboard.php");
                exit();
            } elseif ($user['role'] === 'jobseeker') {
                header("Location: job_seeker/dashboard.php");
                exit();
            } else {
                header("Location: dashboard.php");
                exit();
            }

        } else {
            $error = "Invalid password.";
        }

    } else {
        $error = "No account found with that email.";
    }

    $stmt->close();
}
?>

<div class="login-section">

    <div class="form-container">

        <h2>Welcome Back</h2>
        <p class="subtitle">
            Sign in to access your HireConnect account
        </p>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

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


            <?php
                if (isset($_POST['remember'])) {

    $token = bin2hex(random_bytes(32));

    $stmt = $conn->prepare("UPDATE login SET remember_token=? WHERE id=?");
    $stmt->bind_param("si", $token, $user['id']);
    $stmt->execute();

    setcookie(
        "remember_token",
        $token,
        time() + (86400 * 30), // 30 days
        "/",
        "",
        false,
        true
    );
}
?>

                <a href="forgotten_password.php" class="forgot-link">
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