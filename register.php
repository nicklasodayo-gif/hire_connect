<?php

$message = "";

/* Database Connection */
$conn = new mysqli(
    "localhost",
    "root",
    "",
    "hireconnect"
);

/* Check Connection */
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* Form Submission */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    // Hash password
    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    // Check if email already exists
    $check = $conn->prepare(
        "SELECT id FROM users WHERE email = ?"
    );

    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {

        $message = "Email already registered.";

    } else {

    $check = $conn->prepare(
    "SELECT id FROM users WHERE email = ?"
);

if (!$check) {
    die("Prepare Error: " . $conn->error);
}

$check->bind_param("s", $email);

        $stmt->bind_param(
            "ssss",
            $username,
            $email,
            $hashedPassword,
            $role
        );

        if ($stmt->execute()) {

            $message = "Account created successfully!";

            // Optional redirect
            // header("Location: login.php");
            // exit();

        } else {

            $message = "Registration failed.";
        }

        $stmt->close();
    }

    $check->close();
}

$conn->close();

?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | HireConnect</title>

```
<link rel="stylesheet" href="assets/login.css">

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>
```

</head>

<body>

<header>
    <a href="index.php" class="floating-home">🏠</a>
</header>

<div class="register_container">

```
<h1>HireConnect</h1>

<div class="subtitle">
    Find Your Dream Job Today
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-info">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<form action="" method="POST">

    <input
        type="text"
        name="username"
        placeholder="Username"
        required
    >

    <input
        type="email"
        name="email"
        placeholder="Email Address"
        required
    >

    <input
        type="password"
        name="password"
        id="password"
        placeholder="Create Password"
        required
    >

    <div class="show-password">
        <label>
            <input
                type="checkbox"
                onclick="togglePassword()"
            >
            Show Password
        </label>
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

    <br>

    <button type="submit">
        Create Account
    </button>

    <div class="link mt-3">
        Already have an account?
        <a href="login.php">Login</a>
    </div>

</form>
```

</div>

<script>
function togglePassword() {

    const passwordField =
        document.getElementById("password");

    passwordField.type =
        passwordField.type === "password"
            ? "text"
            : "password";
}
</script>

</body>
</html>
