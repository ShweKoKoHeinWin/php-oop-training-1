<?php

use Classes\Route;
use Classes\User;
use Classes\Session;

var_dump(User::auth());
if (!User::auth()) {
    // header('location:index.php?route=login');
    Route::redirect('login');
    exit();
}
Session::unset('user');

Route::redirect('login');
// header('location:index.php?route=login');
exit;
