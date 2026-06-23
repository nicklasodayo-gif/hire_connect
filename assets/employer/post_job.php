<?php
include "../connect.php";
include "../header.php";

$message = "";

if(isset($_POST['post_job'])){

    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $salary = $_POST['salary'];

    $employer_id = 1; // later replace with SESSION login

    $sql = "INSERT INTO jobs (employer_id, title, description, location, salary)
            VALUES ('$employer_id','$title','$description','$location','$salary')";

    if(mysqli_query($conn, $sql)){
        $message = "Job posted successfully!";
    }
}
?>

<div class="form-container">

<h2>Post a Job</h2>

<?php if($message) echo "<p style='color:green;'>$message</p>"; ?>

<form method="POST">

    <input type="text" name="title" placeholder="Job Title" required>
    <textarea name="description" placeholder="Job Description" required></textarea>
    <input type="text" name="location" placeholder="Location" required>
    <input type="text" name="salary" placeholder="Salary Range" required>

    <button type="submit" name="post_job">Post Job</button>

</form>

</div>

<?php include "../footer.php"; ?>