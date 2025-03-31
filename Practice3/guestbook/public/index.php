<?php

require_once '../controllers/GuestbookController.php';
require_once '../controllers/RegisterController.php';
require_once '../controllers/AdminController.php';
require_once '../controllers/LoginController.php';
require_once '../controllers/LogoutController.php';

use guestbook\public\controllers\AdminController;
use guestbook\public\controllers\GuestbookController;
use guestbook\public\controllers\RegisterController;
use guestbook\public\controllers\LoginController;
use guestbook\public\controllers\LogoutController;

switch ($_SERVER['REQUEST_URI']) {
    case '/':
        $controller = new GuestbookController();
        break;
    case '/register':
        $controller = new RegisterController();
        break;
    case '/admin':
        $controller = new AdminController();
        break;
    case '/login':
        $controller = new LoginController();
        break;
    case '/logout':
        $controller = new LogoutController();
        break;
    default:
        http_response_code(404);
        die('404 Not Found');
}

$controller->execute();

