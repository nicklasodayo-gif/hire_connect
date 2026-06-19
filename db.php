<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "hireconnect"
);

if($conn->connect_error){
    die("Connection Failed");
}
?>