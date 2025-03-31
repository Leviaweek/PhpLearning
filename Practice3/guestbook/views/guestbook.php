<?php

use Guestbook\Core\Database;
function renderGuestBook(): void
{
    try {
        $db = Database::getInstance();
        $connection = $db->getConnection();

        $stmt = $connection->prepare('SELECT name, email, comment, created_at FROM comments');

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc())
        {
            $name = htmlspecialchars($row['name']);
            $email = htmlspecialchars($row['email']);
            $comment = htmlspecialchars($row['comment']);
            $created_at = htmlspecialchars($row['created_at']);
            echo <<< HTML
                <div class="list-group-item list-group-item-action mb-2 shadow-sm">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 text-primary">$name</h6>
                        <small class="text-muted">$email</small>
                    </div>
                    <p class="mb-1">$comment</p>
                    <small class="text-muted">$created_at</small>
                </div>
                HTML;
        }

    } catch (\Throwable $th) {
        echo $th->getMessage();
    }
    finally {
        $connection->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

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
                <form method="POST" action="/guestbook">
                    <input type="hidden" name="form_token" value="<?php echo $_SESSION['form_token']; ?>">
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
                    <?php renderGuestBook() ?>

                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
