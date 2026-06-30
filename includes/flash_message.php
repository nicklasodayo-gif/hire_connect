<?php

if(isset($_SESSION['flash'])){

?>

<div class="alert alert-<?= $_SESSION['flash']['type']; ?> alert-dismissible fade show">

<?= htmlspecialchars($_SESSION['flash']['message']); ?>

<button
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php

unset($_SESSION['flash']);

}

?>