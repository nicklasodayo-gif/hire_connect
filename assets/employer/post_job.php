<?php
include __DIR__ . "/../../connect.php";
include __DIR__ . "/../../header.php";

$message = "";

if(isset($_POST['post_job'])){

    $title = $_POST['title'];
    $category = $_POST['category'];
    $employment_type = $_POST['employment_type'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $salary = $_POST['salary'];
    $deadline = $_POST['deadline'];

    $employer_id = 1;

    /* Logo Upload */
    $logo = "";

    if(!empty($_FILES['logo']['name'])){

        $logo = time() . "_" . $_FILES['logo']['name'];

        move_uploaded_file(
            $_FILES['logo']['tmp_name'],
            "../../uploads/company_logos/" . $logo
        );
    }

    $sql = "INSERT INTO jobs
    (employer_id,title,category,employment_type,
    description,location,salary,logo,deadline)

    VALUES
    ('$employer_id','$title','$category',
    '$employment_type','$description',
    '$location','$salary','$logo','$deadline')";

    if(mysqli_query($conn,$sql)){
        $message = "Job posted successfully!";
    }
}
?>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

<style>

body{
    background:#f4f6f9;
    font-family:Arial, sans-serif;
}

/* Dashboard Layout */

.dashboard{
    display:flex;
    min-height:100vh;
}

/* Sidebar */

.sidebar{
    width:260px;
    background:#1e3a8a;
    color:white;
    padding:20px;
}

.sidebar h2{
    text-align:center;
    margin-bottom:30px;
}

.sidebar ul{
    list-style:none;
    padding:0;
}

.sidebar ul li{
    margin:15px 0;
}

.sidebar ul li a{
    color:white;
    text-decoration:none;
    display:block;
    padding:12px;
    border-radius:8px;
    transition:0.3s;
}

.sidebar ul li a:hover{
    background:#2563eb;
}

/* Main Content */

.content{
    flex:1;
    padding:40px;
}

.form-container{
    max-width:900px;
    margin:auto;
    background:white;
    padding:35px;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
}

.form-container h2{
    text-align:center;
    color:#1e3a8a;
    margin-bottom:25px;
}

.success{
    background:#dcfce7;
    color:#166534;
    padding:12px;
    border-radius:8px;
    margin-bottom:20px;
}

.form-group{
    margin-bottom:18px;
}

.form-group label{
    display:block;
    margin-bottom:6px;
    font-weight:bold;
}

.form-group input,
.form-group select{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:8px;
}

.btn-submit{
    width:100%;
    background:#2563eb;
    color:white;
    border:none;
    padding:14px;
    border-radius:8px;
    cursor:pointer;
    font-size:16px;
}

.btn-submit:hover{
    background:#1d4ed8;
}

</style>

<div class="dashboard">

    <!-- SIDEBAR -->

    <div class="sidebar">

        <h2><i class="bi bi-briefcase-fill"></i> Employer</h2>

        <ul>

            <li>
                <a href="dashboard.php">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>

            <li>
                <a href="post_job.php">
                    <i class="bi bi-plus-circle"></i>
                    Post Job
                </a>
            </li>

            <li>
                <a href="my_jobs.php">
                    <i class="bi bi-list-task"></i>
                    My Jobs
                </a>
            </li>

            <li>
                <a href="view_applicants.php">
                    <i class="bi bi-people"></i>
                    Applicants
                </a>
            </li>

            <li>
                <a href="interviews.php">
                    <i class="bi bi-calendar-event"></i>
                    Interviews
                </a>
            </li>

        </ul>

    </div>

    <!-- CONTENT -->

    <div class="content">

        <div class="form-container">

            <h2>
                <i class="bi bi-plus-square-fill"></i>
                Post New Job
            </h2>

            <?php if($message): ?>
                <div class="success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST"
            enctype="multipart/form-data">

                <div class="form-group">
                    <label>Job Title</label>
                    <input type="text"
                    name="title" required>
                </div>

                <div class="form-group">
                    <label>Job Category</label>

                    <select name="category" required>

                        <option value="">
                            Select Category
                        </option>

                        <option>Software Development</option>
                        <option>Data Science</option>
                        <option>Networking</option>
                        <option>Cyber Security</option>
                        <option>Accounting</option>
                        <option>Marketing</option>
                        <option>Human Resources</option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Employment Type</label>

                    <select
                    name="employment_type"
                    required>

                        <option value="">
                            Select Type
                        </option>

                        <option>Full-Time</option>
                        <option>Part-Time</option>
                        <option>Internship</option>
                        <option>Remote</option>

                    </select>

                </div>

                <div class="form-group">
                    <label>Company Logo</label>

                    <input type="file"
                    name="logo"
                    accept="image/*">
                </div>

                <div class="form-group">

                    <label>Job Description</label>

                    <textarea
                    name="description"
                    id="description"></textarea>

                </div>

                <div class="form-group">
                    <label>Location</label>

                    <input type="text"
                    name="location" required>
                </div>

                <div class="form-group">
                    <label>Salary</label>

                    <input type="text"
                    name="salary" required>
                </div>

                <div class="form-group">
                    <label>Application Deadline</label>

                    <input type="date"
                    name="deadline"
                    required>
                </div>

                <button
                class="btn-submit"
                name="post_job">

                    <i class="bi bi-send-fill"></i>
                    Post Job

                </button>

            </form>

        </div>

    </div>

</div>

<script>
CKEDITOR.replace('description');
</script>

<?php include __DIR__ . "/../../footer.php"; ?>