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

    <h2><a href="index.php?route=login"></a></h2>
    <form action="" method="POST">
        <label for="name">Name</label>
        <input type="text" id="name" class="name" name="name" value="<?php echo isset($old['name']) ? $old['name'] : '' ?>">
        <p>
            <?php
            if (isset($errors['name'])) {
                echo $errors['name'][0];
            }
            ?>
        </p>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo isset($old['email']) ? $old['email'] : ''; ?>">
        <p>
            <?php
            if (isset($errors['email'])) {
                echo $errors['email'][0];
            }
            ?>
        </p>

        <label for=" phone">Phone</label>
        <input type="number" id="phone" name="phone" value="<?php echo isset($old['phone']) ? $old['phone'] : '' ?>">
        <p>
            <?php
            if (isset($errors['phone'])) {
                echo $errors['phone'][0];
            }
            ?>
        </p>

        <label for=" gender">Gender</label>
        <?php if (isset($old['gender'])) { ?>
            <input type="radio" name="gender" value="1" <?php echo $old['gender'] == 1 ? 'checked' : '' ?>>Male
            <input type="radio" name="gender" value="2" <?php echo $old['gender'] == 2 ? 'checked' : '' ?>>Female
        <?php } else { ?>
            <input type="radio" name="gender" value="1" checked>Male
            <input type="radio" name="gender" value="2">Female
        <?php } ?>
        <p>
            <?php
            if (isset($errors['gender'])) {
                echo $errors['gender'][0];
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

        <label for=" conf-password">Confirm Password</label>
        <input type="password" id="conf-password" name="confirm-password">

        <input type="submit" name="signup" value="Submit">
    </form>
</body>

</html>