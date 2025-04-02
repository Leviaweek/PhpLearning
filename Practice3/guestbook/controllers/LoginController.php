<?php
namespace guestbook\controllers;
use Guestbook\Core\Database;

class LoginController
{
    public function execute()
    {
        ob_start();
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
        extract(
                [
                        'form_token' => $_SESSION['form_token'] ?? '',
                        'infoMessage' => $_SESSION['infoMessage'] ?? '',
                ]
        );

        include '../views/login.php';
        $_SESSION['infoMessage'] = '';
        $_SESSION['form_token'] = '';

        return ob_get_clean();
    }

    public function handleRedirectIfAuthenticated(): void
    {
        if (!empty($_SESSION['auth'])) {
            header('Location: /admin');
        }
    }

    /**
     * @return void
     */
    public function handleFormSubmission(): void
    {
        if ($this->isFormValid()) {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            $this->validateFormData($email, $password);

            unset($_SESSION['form_token']);

            $user = $this->getUserFromDatabase($email);

            if (!$user || !password_verify($password, $user['password'])) {
                error($user ? 'Неверный пароль!' : 'Пользователь не найден!');
                return;
            }

            $this->authenticateUser($user['email']);
        }
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
        return !empty($_POST) && !empty($_POST['form_token']) && !empty($_SESSION['form_token']) && $_POST['form_token'] === $_SESSION['form_token'];
    }

    /**
     * @param string $email
     * @return string
     */
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

    /**
     * @return void
     */
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
        exit;
    }
}
