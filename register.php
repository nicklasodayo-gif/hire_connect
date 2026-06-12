<?php
include "db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Validate password length
    if (strlen($password) < 6) {

        $message = "Password must be at least 6 characters long.";
        $messageType = "danger";

    } else {

        // Check if email already exists
        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "Email already exists.";
            $messageType = "danger";

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users(fullname, email, password)
                VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "sss",
                $fullname,
                $email,
                $hashedPassword
            );

            if ($stmt->execute()) {

                $message = "Account created successfully.";
                $messageType = "success";

            } else {

                $message = "Failed to create account.";
                $messageType = "danger";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | HireConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    
    <header>
<a href="index.php" class="floating-home">
    🏠
</a>
</header>

<div class="container mt-5">

    <div class="card shadow mx-auto" style="max-width:500px;">

        <div class="card-body">

            <h2 class="text-center mb-4">
                Create Account
            </h2>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <input
                        type="text"
                        name="fullname"
                        class="form-control"
                        placeholder="Full Name"
                        required
                    >
                </div>

                <div class="mb-3">
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Email Address"
                        required
                    >
                </div>

                <div class="mb-3">
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Password (minimum 6 characters)"
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100">
                    Create Account
                </button>

            </form>

            <p class="text-center mt-3">
                Already have an account?
                <a href="login.php">Login</a>
            </p>

        </div>

    </div>

</div>

</body>
</html>