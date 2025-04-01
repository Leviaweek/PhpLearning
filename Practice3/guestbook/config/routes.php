<?php

require_once '../controllers/GuestbookController.php';
require_once '../controllers/RegisterController.php';
require_once '../controllers/AdminController.php';
require_once '../controllers/LoginController.php';
require_once '../controllers/LogoutController.php';

use guestbook\controllers\AdminController;
use guestbook\controllers\GuestbookController;
use guestbook\controllers\RegisterController;
use guestbook\controllers\LoginController;
use guestbook\controllers\LogoutController;

define('ROUTES', [
    '/' => new GuestbookController(),
    '/register' => new RegisterController(),
    '/admin' => new AdminController(),
    '/login' => new LoginController(),
    '/logout' => new LogoutController(),
]);