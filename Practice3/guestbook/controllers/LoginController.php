<?php
namespace guestbook\public\controllers;
use Guestbook\Core\Database;

class LoginController
{
    public function execute(): void
    {
        session_start();

        require_once '../core/error.php';

        $this->handleRedirectIfAuthenticated();

        require_once '../core/Database.php';


        $this->handleFormSubmission();

        $this->ensureFormToken();

        echo $this->renderView();
    }

    private function renderView(): string
    {
        ob_start();

        include dirname(__DIR__) . '/views/login.php';

        unset($_SESSION['infoMessage']);

        return ob_get_clean();
    }

    public function handleRedirectIfAuthenticated(): void
    {
        if (!empty($_SESSION['auth'])) {
            header('Location: /admin');
            exit;
        }
    }

    /**
     * @return void
     */
    public function handleFormSubmission(): void
    {
        if (!$this->isFormValid()) {
            return;
        }

        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $this->validateFormData($email, $password);

        $user = $this->getUserFromDatabase($email);

        if (!$user || !password_verify($password, $user['password'])) {
            error("login", $user ? 'Неверный пароль!' : 'Пользователь не найден!');
            return;
        }

        $this->authenticateUser($user['email']);
    }

    /**
     * @param string $email
     * @param string $password
     * @return void
     */
    public function validateFormData(string $email, string $password): void
    {
        if (empty($email) || empty($password)) {
            error('login', 'Заполните форму авторизации!');
        }

        if (strlen($email) > 320 || strlen($password) > 72) {
            error('login', 'Превышена длина поля!');
        }
    }

    /**
     * @return bool
     */
    public function isFormValid(): bool
    {
        return !empty($_POST) &&
            !empty($_POST['form_token']) &&
            !empty($_SESSION['form_token']) &&
            $_POST['form_token'] === $_SESSION['form_token'];
    }

    public function getUserFromDatabase(string $email): ?array
    {
        $db = Database::getInstance();
        $connection = $db->getConnection();

        $stmt = $connection->prepare('SELECT id, email, password FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);

        $stmt->execute();

        $user = $stmt->get_result()->fetch_assoc();
        $db->close();
        return $user;
    }

    public function ensureFormToken(): void
    {
        if (empty($_SESSION['form_token'])) {
            $_SESSION['form_token'] = bin2hex(random_bytes(32));
        }
    }

    /**
     * @param $email
     * @return void
     */
    public function authenticateUser($email): void
    {
        $_SESSION['auth'] = true;
        $_SESSION['email'] = $email;

        header('Location: /admin');
    }
}
