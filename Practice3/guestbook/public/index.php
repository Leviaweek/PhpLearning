<?php

require_once dirname(__DIR__) . '/config/routes.php';

$controller = ROUTES[$_SERVER['REQUEST_URI']] ?? null;
if ($controller === null) {
    http_response_code(404);
    die('404 Not Found');
}

if(!method_exists($controller,'execute')){
    http_response_code(500);
    die('500 Internal Server Error');
}

$controller->execute();