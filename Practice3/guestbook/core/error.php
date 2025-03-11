<?php 

function error(string $location, string $message = '') {
    if (!empty($message)) {
        $_SESSION['infoMessage'] = $message;
    }
    header("Location: $location");
    exit;
}