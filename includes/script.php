<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Global JS -->
<script src="<?= BASE_URL ?>assets/js/main.js"></script>

<?php

if(isset($page_js)){

    echo '<script src="' .
        BASE_URL .
        $page_js .
        '"></script>';

}