<?php

require_once dirname(__DIR__) . '/config/routes.php';

$controller = ROUTES[$_SERVER['REQUEST_URI']] ?? null;
if ($controller === null) {
    http_response_code(404);
    die('404 Not Found');
}
$controller->execute();