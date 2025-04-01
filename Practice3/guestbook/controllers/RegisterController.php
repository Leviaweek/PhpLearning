<?php
namespace guestbook\controllers;
use Guestbook\Core\Database;

class RegisterController
{
    public function execute()
    {
        ob_start();
        session_start();

        require_once '../core/error.php';

        $this->handleRedirectIfAuthenticated();

        require_once "../core/Database.php";

        $this->handleFormSubmission();

        $this->ensureFormToken();

        echo $this->renderView();
    }

    public function renderView(): string
    {
        ob_start();

        include dirname(__DIR__) . '/views/register.php';

        unset($_SESSION['infoMessage']);

        return ob_get_clean();
    }

    public function handleRedirectIfAuthenticated(): void
    {
        if (!empty($_SESSION['auth'])) {
            header('Location: /admin');
        }
    }

    public function handleFormSubmission(): void
    {
        if ($this->isValidFormData()) {

            $email = trim($_POST['email']);
            $password_trim = trim($_POST['password']);

            $this->validateFormValues($email, $password_trim);

            $password_hash = password_hash($password_trim, PASSWORD_DEFAULT);

            $db = Database::getInstance();
            $connection = $db->getConnection();

            $user = $this->getUser($connection, $email);

            if ($user) {
                error('register', 'Такой пользователь уже существует!');
            } else {
                $this->insertUser($connection, $email, $password_hash);
                header('Location: /login');
                exit;
            }

        } else if (!empty($_POST['form_token'])) {
            error('register', 'Заполните форму регистрации!');
        }
    }

    public function isValidFormData(): bool
    {
        return !empty($_POST) &&
            !empty($_POST['form_token']) &&
            !empty($_SESSION['form_token']) &&
            $_POST['form_token'] === $_SESSION['form_token'];
    }

    public function validateFormValues(string $email, string $password_trim): void
    {
        if (empty($email) || empty($password_trim)) {
            error('register', 'Заполните форму регистрации!');
        }

        if (strlen($email) > 320 || strlen($password_trim) > 72) {
            error('register', 'Превышена длина поля!');
        }
    }

    public function getUser(\mysqli $connection, string $email): ?array
    {
        $stmt = $connection->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);

        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        return $user;
    }

    public function insertUser(\mysqli $connection, string $email, string $password_hash): void
    {
        $stmt = $connection->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
        $stmt->bind_param('ss', $email, $password_hash);

        if (!$stmt->execute()) {
            error('register', 'Регистрация не удалась!');
        }
        $_SESSION['id'] = $connection->insert_id;
        $_SESSION['auth'] = true;
        $_SESSION['email'] = $email;
    }

    public function ensureFormToken(): void
    {
        if (empty($_SESSION['form_token'])) {
            $_SESSION['form_token'] = bin2hex(random_bytes(32));
        }
    }
}