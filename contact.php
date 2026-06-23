<?php

$page_css = "assets\contact.css";
include 'header.php';

?>

<?php


$message = "";

if(isset($_POST['send'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $msg = $_POST['message'];

    $sql = "INSERT INTO contacts
    (name,email,subject,message)
    VALUES
    ('$name','$email','$subject','$msg')";

    if(mysqli_query($conn,$sql)){
        $message = "Message sent successfully!";
    }
}
?>


<div class="container">

<h2>Contact Us</h2>

<?php if(!empty($message)): ?>
    <div class="success-message">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<form method="POST">

    <input
        type="text"
        name="name"
        placeholder="Your Name"
        required>

    <input
        type="email"
        name="email"
        placeholder="Email Address"
        required>

    <input
        type="text"
        name="subject"
        placeholder="Subject"
        required>

    <textarea
        name="message"
        placeholder="Message"
        required></textarea>

    <button
        type="submit"
        name="send">
        Send Message
    </button>

</form>
</div>

<script src=""></script>

<?php include 'footer.php'; ?>