<?php
// TODO 1: PREPARING ENVIRONMENT: 1) session 2) functions
session_start();

// TODO 2: ROUTING

require_once '../core/Database.php';

// TODO 3: CODE by REQUEST METHODS (ACTIONS) GET, POST, etc. (handle data from request): 1) validate 2) working with data source 3) transforming data

if (!empty($_POST))
{
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $comment = trim($_POST['comment']);

    if (empty($name) || empty($email) || empty($comment))
    {
        header("Location: guestbook.php");
        die;
    }

    $db = Database::getInstance();
    $connection = $db->getConnection();

    $stmt = $connection->prepare('INSERT INTO comments (name, email, comment) VALUES (:name, :email, :comment)');
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':comment', $comment, SQLITE3_TEXT);

    $stmt->execute();

    header("Location: guestbook.php");
    die;
}

// TODO 4: RENDER: 1) view (html) 2) data (from php)


?>

<!DOCTYPE html>
<html>

<?php require_once '../core/sectionHead.php' ?>

<body>

<div class="container">

    <!-- navbar menu -->
    <?php require_once '../core/sectionNavbar.php' ?>
    <br>

    <!-- guestbook section -->
    <div class="card card-primary">
        <div class="card-header bg-primary text-light">
            GuestBook form
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-sm-6">

                 <!-- TODO: create guestBook html form   -->
                 <form method="POST" action="guestbook.php">
                    <div class="mb-3">
                        <label for="name" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Введите ваше имя" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Введите ваш email" required>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Комментарий</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" placeholder="Введите ваш комментарий" style="max-height: 200px;" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Отправить</button>
                </form>

                </div>
            </div>

        </div>
    </div>

    <br>

    <div class="card card-primary">
        <div class="card-header bg-body-secondary text-dark">
            Сomments
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">

                    <!-- TODO: render guestBook comments   -->
                    <?php

                        try {
                            $db = Database::getInstance();
                            $connection = $db->getConnection();

                            $stmt = $connection->prepare('SELECT name, email, comment, created_at FROM comments');

                            $result = $stmt->execute();

                            while ($row = $result->fetchArray(SQLITE3_ASSOC))
                            {
                                echo <<< HTML
                                <div class="list-group-item list-group-item-action mb-2 shadow-sm">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 text-primary">{$row['name']}</h6>
                                        <small class="text-muted">{$row['created_at']}</small>
                                    </div>
                                    <p class="mb-1">{$row['comment']}</p>
                                    <small class="text-muted">{$row['email']}</small>
                                </div>
                                HTML;
                            }

                        } catch (\Throwable $th) {
                            echo $th->getMessage();
                        }
                        finally {
                            $connection->close();
                        }

                     ?>

                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
