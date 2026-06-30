<?php

$page_title = $page_title ?? APP_NAME;

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

<?= htmlspecialchars($page_title); ?>

</title>

<?php require_once __DIR__ . "/styles.php"; ?>

</head>

<body>