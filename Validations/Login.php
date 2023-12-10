<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php

    use Classes\Route;
    use Classes\User;
    use Classes\Session;

    if (User::auth()) {
        Route::redirect('home');
        exit;
    }
    $old = [];
    if (isset($_POST) && count($_POST) > 0) {
        foreach ($_POST as $key => $value) {
            $old[$key] = $value;
        }
    }

    $auth = User::authenticate();
    if (isset($auth) && !empty($auth)) {
        Route::redirect('home');
    }
    ?>

    <form action="" method="POST">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo isset($old['email']) ? $old['email'] : ''; ?>">
        <p>
            <?php
            if (isset($errors['email'])) {
                echo $errors['email'][0];
            }
            ?>
        </p>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" value="<?php echo isset($old['password']) ? $old['password'] : '' ?>">
        <p>
            <?php
            if (isset($errors['password'])) {
                echo $errors['password'][0];
            }
            ?>
        </p>

        <input type="submit" name="login" value="Submit">
    </form>
</body>

</html>