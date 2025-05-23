<?php
namespace guestbook\controllers;

class LogoutController
{
    public function execute(): void
    {
        ob_start();
        session_start();

        session_destroy();
        header('Location: /');
    }
}