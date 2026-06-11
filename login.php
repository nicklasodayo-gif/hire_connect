<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HireConnect Login</title>

    <link rel="stylesheet" href="assets/login.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>

<div class="container">

    <h1>HireConnect</h1>
    <div class="subtitle">Find Your Dream Job Today</div>

    <?php if (!empty($message)): ?>
    <div class="alert alert-danger">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

    <form action="login.php" method="POST">

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

        <div class="remember">
            <label>
                <input type="checkbox" name="remember">
                Remember Me
            </label>
        </div>

        <div class="forgot">
            <a href="#">Forgot Password?</a>
        </div>

        <button type="submit">LOGIN</button>

        <div class="or">----------- OR -----------</div>

        <div class="social">
            <button type="button">Continue with Google</button>
            <button type="button">Continue with LinkedIn</button>
        </div>

        <div class="link">
            Don't have an account?
            <a href="regester.php">Create Account</a>
        </div>

        <div class="roles">
            <p>Select Account Type:</p>

            <label>
                <input
                    type="radio"
                    name="role"
                    value="jobseeker"
                    checked
                >
                Job Seeker
            </label>

            <br>

            <label>
                <input
                    type="radio"
                    name="role"
                    value="employer"
                >
                Employer
            </label>
        </div>

    </form>

</div>
<?php
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $role = $_POST["role"];

    // Demo accounts
    $users = [
        [
            "email" => "jobseeker@hireconnect.com",
            "password" => "123456",
            "role" => "jobseeker"
        ],
        [
            "email" => "employer@hireconnect.com",
            "password" => "123456",
            "role" => "employer"
        ]
    ];

    $authenticated = false;

    foreach ($users as $user) {

        if (
            $user["email"] === $email &&
            $user["password"] === $password &&
            $user["role"] === $role
        ) {

            $_SESSION["email"] = $email;
            $_SESSION["role"] = $role;

            $authenticated = true;

            if ($role === "jobseeker") {
                header("Location: dashboard.php");
                exit();
            } else {
                header("Location: employer-dashboard.php");
                exit();
            }
        }
    }

    if (!$authenticated) {
        $message = "Invalid email, password, or account type.";
    }
}
?>

<script src="assets/scripts.js"></script>

</body>
</html>