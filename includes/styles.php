<?php

$page_css = $page_css ?? "";

?>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- Global CSS -->
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">

<?php

if (!empty($page_css)) {

    echo '<link rel="stylesheet" href="' .
        BASE_URL .
        $page_css .
        '">';

}