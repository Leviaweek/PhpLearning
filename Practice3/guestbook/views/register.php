<?php
function drawInfoMessage() : void
{
    if (empty($_SESSION['infoMessage'])) {
        return;
    }
    echo <<< HTML
        <hr/>
        <span style='color:red'>{$_SESSION['infoMessage']}</span>
        HTML;
    $_SESSION['infoMessage'] = '';
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
            <form method="post" action="/register">
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