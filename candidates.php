<?php

$search = $_GET["search"] ?? "";

$candidates = [
    [
        "fullname" => "Nicklas Odayo",
        "email" => "nicklas@gmail.com",
        "skills" => "PHP, JavaScript, HTML, CSS",
        "course" => "Information Technology"
    ],
    [
        "fullname" => "Jane Akinyi",
        "email" => "jane@gmail.com",
        "skills" => "Graphic Design, UI/UX",
        "course" => "Computer Science"
    ],
    [
        "fullname" => "John Otieno",
        "email" => "john@gmail.com",
        "skills" => "Python, Data Analysis",
        "course" => "Data Science"
    ]
];

$found = false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Candidates</title>

    <link rel="stylesheet" href="assets\candindtes.css">
</head>
<body>

<div class="container">

    <h1>Search Candidates</h1>

    <form method="GET">

        <input
            type="text"
            id="searchInput"
            name="search"
            value="<?php echo htmlspecialchars($search); ?>"
            placeholder="Search by name, skill or course..."
        >

        <button type="submit">
            Search
        </button>

    </form>

    <div class="results">

        <?php foreach ($candidates as $candidate): ?>

            <?php
            if (
                $search &&
                stripos($candidate["fullname"], $search) === false &&
                stripos($candidate["skills"], $search) === false &&
                stripos($candidate["course"], $search) === false
            ) {
                continue;
            }

            $found = true;
            ?>

            <div class="candidate-card">

                <h3>
                    <?php echo htmlspecialchars($candidate["fullname"]); ?>
                </h3>

                <p>
                    <strong>Email:</strong>
                    <?php echo htmlspecialchars($candidate["email"]); ?>
                </p>

                <p>
                    <strong>Skills:</strong>
                    <?php echo htmlspecialchars($candidate["skills"]); ?>
                </p>

                <p>
                    <strong>Course:</strong>
                    <?php echo htmlspecialchars($candidate["course"]); ?>
                </p>

            </div>

        <?php endforeach; ?>

        <?php if (!$found): ?>
            <p>No candidates found.</p>
        <?php endif; ?>

    </div>

</div>

<script src="assets/candidates.js"></script>

</body>
</html>