<?php

function drawInfoMessage(): void
{
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
<html lang="en">

<?php require_once dirname(__DIR__, 1) . '/core/sectionHead.php'; ?>

<body>

<div class="container">

    <?php require_once '../core/sectionNavbar.php' ?>

    <br>

    <div class="card card-primary">
        <div class="card-header bg-primary text-light">
            Login form
        </div>
        <div class="card-body">
            <form method="post" action="/login">
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
