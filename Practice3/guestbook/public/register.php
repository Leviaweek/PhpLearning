<?php
// TODO 1: PREPARING ENVIRONMENT: 1) session 2) functions
ob_start();
session_start();

require_once '../core/error.php';

// TODO 2: ROUTING
if (!empty($_SESSION['auth'])) {
    header('Location: /admin.php');
}

// TODO 3: CODE by REQUEST METHODS (ACTIONS) GET, POST, etc. (handle data from request): 1) validate 2) working with data source 3) transforming data

require_once "../core/Database.php";

// 2. handle form data
if (!empty($_POST) &&
    !empty($_POST['form_token']) &&
    !empty($_SESSION['form_token']) &&
    $_POST['form_token'] === $_SESSION['form_token']) {
    
    $email = trim($_POST['email']);
    $password_trim = trim($_POST['password']);

    if (empty($email) || empty($password_trim)) {
        error('register.php', 'Заполните форму регистрации!');
    }

    if (strlen($email) > 320 || strlen($password_trim) > 72) {
        error('register.php', 'Превышена длина поля!');
    }

    unset($_SESSION['form_token']);

    $password_hash = password_hash($password_trim, PASSWORD_DEFAULT);

    $db = Database::getInstance();
    $connection = $db->getConnection();
    
    $stmt = $connection->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->fetch_assoc()) {
        error('register.php', 'Такой пользователь уже существует!');
    }
    else {
        $stmt = $connection->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
        $stmt->bind_param('ss', $email, $password_hash);

        if (!$stmt->execute()) {
            error('register.php', 'Регистрация не удалась!');
        }
        $_SESSION['id'] = $connection->insert_id;
        $_SESSION['auth'] = true;
        $_SESSION['email'] = $email;
        header('Location: /login.php');
        exit;
    }

} else if (!empty($_POST['form_token'])) {
    error('register.php', 'Заполните форму регистрации!');
}

if (empty($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}

// TODO 4: RENDER: 1) view (html) 2) data (from php)
function drawInfoMessage() {
    if (!empty($_SESSION['infoMessage'])) {
        echo <<< HTML
        <hr/>
        <span style='color:red'>{$_SESSION['infoMessage']}</span>
        HTML;
        $_SESSION['infoMessage'] = '';
    }
}

?>


<!DOCTYPE html>
<html>

<?php require_once '../core/sectionHead.php' ?>

<body>

<div class="container">

    <?php require_once '../core/sectionNavbar.php' ?>

    <br>

    <div class="card card-primary">
        <div class="card-header bg-success text-light">
            Register form
        </div>
        <div class="card-body">
            <form method="post" action="register.php">
                <input type="hidden" name="form_token" value="<?php echo $_SESSION['form_token']; ?>"/>
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="email" name="email" required/>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input class="form-control" type="password" name="password" required minlength="6"/>
                </div>
                <br>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" name="formRegister"/>
                </div>
            </form>

            <!-- TODO: render php data   -->
            <?php drawInfoMessage() ?>

        </div>

    </div>
</div>

</body>
</html>