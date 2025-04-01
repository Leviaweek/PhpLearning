<?php
namespace guestbook\controllers;

class AdminController
{
    public function execute()
    {
        ob_start();
        session_start();

        if (empty($_SESSION['auth'])) {
            header('Location: /index.php');
            die;
        }

        echo $this->renderView();
    }

    public function renderView(): string
    {
        ob_start();

        include dirname(__DIR__) . '/views/admin.php';

        unset($_SESSION['infoMessage']);

        return ob_get_clean();
    }
}
