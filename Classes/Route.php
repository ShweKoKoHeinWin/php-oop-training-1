<?php

namespace Classes;

class Route
{
    public static function controller()
    {
        global $errors;
        global $register;
        global $login;
        switch ($_GET['route'] ?? '') {
            case 'home':
            case '':

                break;

            case 'signup':
                include_once('Validations/Signup.php');
                break;

            case 'login':
                include_once('Validations/Login.php');
                break;

            case 'logout':
                include_once('Validations/Logout.php');
                break;

            default:
        }
    }

    public static function redirect($route)
    {
        header('location:index.php?route=' . $route);
    }
}
