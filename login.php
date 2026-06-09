<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["fullname"] = $user["fullname"];

            header("Location: index.php");
            exit();

        } else {
            $error = "Incorrect password";
        }

    } else {
        $error = "Account not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <div class="card mx-auto" style="max-width:500px;">
        <div class="card-body">

            <h2>Login</h2>

            <p class="text-danger">
                <?php echo $error; ?>
            </p>

            <form method="POST">

                <input
                    type="email"
                    name="email"
                    class="form-control mb-3"
                    placeholder="Email"
                    required>

                <input
                    type="password"
                    name="password"
                    class="form-control mb-3"
                    placeholder="Password"
                    required>

                <button class="btn btn-primary w-100">
                    Login
                </button>

            </form>

            <p class="mt-3">
                Don't have an account?
                <a href="register.php">Create Account</a>
            </p>

        </div>
    </div>

</div>

</body>
</html>