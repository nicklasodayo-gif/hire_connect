<?php

require_once "mail/Mail.php";

$result = sendInterviewEmail(

    "nicklasodayo@gmail.com",   // Replace with your email

    "John Doe",

    "10 July 2026",

    "10:00 AM",

    "Online",

    "Google Meet",

    "https://meet.google.com/abc-defg-hij"

);

if($result){

    echo "Email sent successfully.";

}else{

    echo "Failed to send email.";

}