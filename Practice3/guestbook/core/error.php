<?php 

function error(string $location, string $message = '') {
    ob_end_clean();
    if (!empty($message)){
        $_SESSION['infoMessage'] = $message;
    }
    header("Location: $location");
    die;
}