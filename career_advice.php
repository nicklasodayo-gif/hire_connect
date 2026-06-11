    <?php
$advice = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $interest = $_POST["interest"] ?? "";
    $skill = htmlspecialchars($_POST["skill"] ?? "");

    switch ($interest) {

        case "technology":
            $advice = "
                <h3>Technology Career Advice</h3>
                <p>Based on your interest in technology and your skill in <strong>$skill</strong>, consider:</p>
                <ul>
                    <li>Software Developer</li>
                    <li>Web Developer</li>
                    <li>Cybersecurity Analyst</li>
                    <li>Data Scientist</li>
                </ul>
            ";
            break;

        case "business":
            $advice = "
                <h3>Business Career Advice</h3>
                <p>Your skills could fit careers such as:</p>
                <ul>
                    <li>Business Analyst</li>
                    <li>Project Manager</li>
                    <li>Marketing Specialist</li>
                    <li>Entrepreneur</li>
                </ul>
            ";
            break;

        case "design":
            $advice = "
                <h3>Design Career Advice</h3>
                <p>You may enjoy careers such as:</p>
                <ul>
                    <li>UI/UX Designer</li>
                    <li>Graphic Designer</li>
                    <li>Animator</li>
                    <li>Product Designer</li>
                </ul>
            ";
            break;

        case "health":
            $advice = "
                <h3>Healthcare Career Advice</h3>
                <p>Potential career paths include:</p>
                <ul>
                    <li>Doctor</li>
                    <li>Nurse</li>
                    <li>Nutritionist</li>
                    <li>Medical Laboratory Technologist</li>
                </ul>
            ";
            break;

        default:
            $advice = "<p>Please select an interest area.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Advice | HireConnect</title>

    <link rel="stylesheet" href="assets/advice.css">
</head>
<body>

<header>
    <h1>Career Advice Center</h1>
    <p>Discover the best career path based on your interests and skills.</p>
</header>

<div class="container">

    <section class="career-form">

        <h2>Career Recommendation Quiz</h2>

        <form method="POST">

            <div class="form-group">
                <label>Interest Area</label>

                <select name="interest" required>
                    <option value="">Choose...</option>
                    <option value="technology">Technology</option>
                    <option value="business">Business</option>
                    <option value="design">Design</option>
                    <option value="health">Health</option>
                </select>
            </div>

            <div class="form-group">
                <label>Favorite Skill</label>

                <input
                    type="text"
                    name="skill"
                    placeholder="Coding, Communication, Design..."
                    required
                >
            </div>

            <button type="submit">
                Get Career Advice
            </button>

        </form>

        <?php if (!empty($advice)): ?>
            <div id="result">
                <?php echo $advice; ?>
            </div>
        <?php endif; ?>

    </section>

</div>

<footer>
    <p>&copy; 2026 HireConnect. All Rights Reserved.</p>
</footer>

</body>
</html>