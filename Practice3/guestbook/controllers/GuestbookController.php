<?php
namespace guestbook\controllers;
use Guestbook\Core\Database;

class GuestbookController
{
    public function execute(): void
    {
        ob_start();
        session_start();

        require_once '../core/error.php';
        require_once '../core/Database.php';

        $this->handleFormSubmission();

        $this->ensureFormToken();

        echo $this->renderView();

    }

    private function renderView(): string
    {
        ob_start();

        include dirname(__DIR__) . '/views/guestbook.php';

        unset($_SESSION['infoMessage']);

        return ob_get_clean();
    }

    /**
     * @return void
     */
    public function handleFormSubmission(): void
    {
        if (!$this->IsValidFormData()) {
            return;
        }

        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $comment = trim($_POST['comment']);

        $this->validateFormValues($name, $email, $comment);

        $this->insertValue($name, $email, $comment);

        header("Location: /");
    }

    /**
     * @return bool
     */
    public function IsValidFormData(): bool
    {
        return !empty($_POST) && !empty($_POST['form_token']) && $_POST['form_token'] === $_SESSION['form_token'];
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $comment
     * @return void
     */
    public function validateFormValues(string $name, string $email, string $comment): void
    {
        if (empty($name) || empty($email) || empty($comment)) {
            error('guestbook', 'Заполните все поля формы!');
        }

        if (strlen($name) > 75 || strlen($email) > 320 || strlen($comment) > 500) {
            error('guestbook', 'Превышена длина поля!');
        }
    }

    public function insertValue(string $name, string $email, string $comment): void
    {
        $db = Database::getInstance();
        $connection = $db->getConnection();

        $stmt = $connection->prepare('INSERT INTO comments (name, email, comment) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $name, $email, $comment);

        $stmt->execute();
        $db->close();
    }

    public function ensureFormToken(): void
    {
        if (empty($_SESSION['form_token'])) {
            $_SESSION['form_token'] = bin2hex(random_bytes(32));
        }
    }
}