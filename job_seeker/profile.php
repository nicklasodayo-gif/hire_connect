<?php
include("includes/auth.php");

$user_id = $_SESSION['user_id'];
$message = "";

// Fetch user + profile
$query = mysqli_query($conn,"
SELECT users.*, jobseekers.*
FROM users
LEFT JOIN jobseekers
ON users.user_id = jobseekers.user_id
WHERE users.user_id='$user_id'
");

$user = mysqli_fetch_assoc($query);

// UPDATE PROFILE
if(isset($_POST['update_profile'])){

    $full_name = mysqli_real_escape_string($conn,$_POST['full_name']);
    $phone = mysqli_real_escape_string($conn,$_POST['phone']);
    $skills = mysqli_real_escape_string($conn,$_POST['skills']);
    $education = mysqli_real_escape_string($conn,$_POST['education']);
    $experience = mysqli_real_escape_string($conn,$_POST['experience']);
    $bio = mysqli_real_escape_string($conn,$_POST['bio']);
    $location = mysqli_real_escape_string($conn,$_POST['location']);

    mysqli_query($conn,"
    UPDATE users
    SET
    full_name='$full_name',
    phone='$phone'
    WHERE user_id='$user_id'
    ");

    // Upload Profile Photo
    $profile_photo = $user['profile_photo'];

    if(!empty($_FILES['profile_photo']['name'])){

        $photo = time()."_".$_FILES['profile_photo']['name'];

        move_uploaded_file(
            $_FILES['profile_photo']['tmp_name'],
            "../uploads/profiles/".$photo
        );

        $profile_photo = $photo;
    }

    // Upload Resume
    $resume = $user['resume'];

    if(!empty($_FILES['resume']['name'])){

        $resumeFile = time()."_".$_FILES['resume']['name'];

        move_uploaded_file(
            $_FILES['resume']['tmp_name'],
            "../uploads/resumes/".$resumeFile
        );

        $resume = $resumeFile;
    }

    mysqli_query($conn,"
    INSERT INTO jobseekers
    (
        user_id,
        profile_photo,
        resume,
        skills,
        education,
        experience,
        bio,
        location
    )

    VALUES(

        '$user_id',
        '$profile_photo',
        '$resume',
        '$skills',
        '$education',
        '$experience',
        '$bio',
        '$location'

    )

    ON DUPLICATE KEY UPDATE

        profile_photo='$profile_photo',
        resume='$resume',
        skills='$skills',
        education='$education',
        experience='$experience',
        bio='$bio',
        location='$location'
    ");

    $message = "Profile updated successfully.";

    header("Refresh:1");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>My Profile</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/jobseeker.css">

</head>

<body>

<?php include("includes/sidebar.php"); ?>

<div class="main-content">

<?php include("includes/topbar.php"); ?>

<div class="container mt-4">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>

<i class="bi bi-person-circle"></i>

My Profile

</h4>

</div>

<div class="card-body">

<?php if($message!=""){ ?>

<div class="alert alert-success">

<?= $message; ?>

</div>

<?php } ?>

<form method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-4 text-center">

<?php

$image="../uploads/profiles/default.png";

if(!empty($user['profile_photo'])){

$image="../uploads/profiles/".$user['profile_photo'];

}

?>

<img src="<?= $image; ?>"

class="img-fluid rounded-circle mb-3"

style="width:180px;height:180px;object-fit:cover;">

<input
type="file"
name="profile_photo"
class="form-control">

</div>

<div class="col-md-8">

<div class="mb-3">

<label>Full Name</label>

<input
type="text"
name="full_name"
class="form-control"
value="<?= htmlspecialchars($user['full_name']); ?>">

</div>

<div class="mb-3">

<label>Email</label>

<input
type="email"
class="form-control"
value="<?= htmlspecialchars($user['email']); ?>"
readonly>

</div>

<div class="mb-3">

<label>Phone</label>

<input
type="text"
name="phone"
class="form-control"
value="<?= htmlspecialchars($user['phone']); ?>">

</div>

<div class="mb-3">

<label>Location</label>

<input
type="text"
name="location"
class="form-control"
value="<?= htmlspecialchars($user['location'] ?? ''); ?>">

</div>

</div>

</div>

<hr>

<div class="mb-3">

<label>Skills</label>

<textarea
name="skills"
rows="3"
class="form-control"><?= htmlspecialchars($user['skills'] ?? ''); ?></textarea>

</div>

<div class="mb-3">

<label>Education</label>

<textarea
name="education"
rows="3"
class="form-control"><?= htmlspecialchars($user['education'] ?? ''); ?></textarea>

</div>

<div class="mb-3">

<label>Experience</label>

<textarea
name="experience"
rows="3"
class="form-control"><?= htmlspecialchars($user['experience'] ?? ''); ?></textarea>

</div>

<div class="mb-3">

<label>Professional Bio</label>

<textarea
name="bio"
rows="5"
class="form-control"><?= htmlspecialchars($user['bio'] ?? ''); ?></textarea>

</div>

<div class="mb-3">

<label>Resume (PDF)</label>

<input
type="file"
name="resume"
accept=".pdf"
class="form-control">

<?php

if(!empty($user['resume'])){

?>

<div class="mt-2">

<a
href="../uploads/resumes/<?= htmlspecialchars($user['resume']); ?>"
target="_blank"
class="btn btn-success btn-sm">

<i class="bi bi-file-earmark-pdf"></i>

View Current Resume

</a>

</div>

<?php } ?>

</div>

<button
type="submit"
name="update_profile"
class="btn btn-primary">

<i class="bi bi-check-circle"></i>

Save Changes

</button>

</form>

</div>

</div>

</div>

<?php include("includes/footer.php"); ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>