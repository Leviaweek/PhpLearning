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

require_once '../core/Database.php';

// 2. handle form data
if (!empty($_POST) && !empty($_POST['form_token']) && !empty($_SESSION['form_token']) && $_POST['form_token'] === $_SESSION['form_token']) {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        error('login.php', 'Заполните форму авторизации!');
    }

    if (strlen($email) > 320 || strlen($password) > 72) {  
        error('login.php', 'Превышена длина поля!');
    }

    unset($_SESSION['form_token']);

    $db = Database::getInstance();
    $connection = $db->getConnection();

    $stmt = $connection->prepare('SELECT id, email, password FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();
    $db->close();

    if (empty($user)) {
        error('login.php', 'Такой пользователь не существует!');
    }

    if (password_verify(password: $password, hash: $user['password'])) {
        $_SESSION['auth'] = true;
        $_SESSION['email'] = $user['email'];

        header('Location: /admin.php');
        exit;
    }
    else {
        error('login.php', 'Неверный пароль!');
    }

} elseif (!empty($_POST['form_token'])) {
    error('login.php', 'Заполните форму авторизации!');
}

if (empty($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}

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
            <div class="card-header bg-primary text-light">
                Login form
            </div>
            <div class="card-body">
                <form method="post" action="login.php">
                    <input type="hidden" name="form_token" value="<?php echo $_SESSION['form_token']; ?>"/>
                    <div class="form-group">
                        <label>Email</label>
                        <input class="form-control" type="email" name="email"/>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input class="form-control" type="password" name="password"/>
                    </div>
                    <br>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" name="form"/>
                    </div>
                </form>

                <!-- TODO: render php data   -->
                <?php drawInfoMessage(); ?>

            </div>
        </div>
    </div>


</body>
</html>

