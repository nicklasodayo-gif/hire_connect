<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users(fullname,email,password)
            VALUES('$fullname','$email','$password')";

    if (mysqli_query($conn, $sql)) {
        $message = "Account created successfully.";
    } else {
        $message = "Email already exists.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Account</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <div class="card mx-auto" style="max-width:500px;">
        <div class="card-body">

            <h2>Create Account</h2>

            <p class="text-success">
                <?php echo $message; ?>
            </p>

            <form method="POST">

                <input
                    type="text"
                    name="fullname"
                    class="form-control mb-3"
                    placeholder="Full Name"
                    required>

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
                    Create Account
                </button>

            </form>

            <p class="mt-3">
                Already have an account?
                <a href="login.php">Login</a>
            </p>

        </div>
    </div>

</div>

</body>
</html>