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
