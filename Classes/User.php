<?php

namespace Classes;

class User
{
    protected static $instance;
    protected $columns;
    protected static $rules = [
        'name' => 'required|max:13|min:5',
        'email' => 'required|email',
        'password' => 'required|min:8|confirm',
        'gender' => 'required',
        'phone' => 'required'
    ];
    public function __construct()
    {
        // $db = new Db;
        // $db->table('users');
        // var_dump($db->getColumns());
        $this->columns = DB::table('users')->getColumns();
    }
    public static function action()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    public static function find($id)
    {
        return DB::table('users')->find($id)->get();
    }
    public static function create($array)
    {
        // die();
        Validate::rules($array, self::$rules);
        if (!empty(Validate::getErrors()) && count(Validate::getErrors()) > 0) {
            return DB::table('users')->create($array);
        }
        $result = DB::table('users')->create($array);
        if (is_int($result)) {

            Session::setSession('user', ['userId' => $result, 'userName' => $array['name'], 'login' => 1]);
            Session::regenerate();
            // $_SESSION['user']['userId'] = $result;
            // $_SESSION['user']['userName'] = $array['name'];
            // $_SESSION['user']['login'] = $array['name'];
        }
        return $result;
    }
    public static function get($array)
    {
        // return DB::table('users')->where($key, $value);
    }

    public static function __callStatic($funName, $params)
    {
        $col = str_replace('get_by_', '', $funName);
        $val = $params[0];
        if (in_array($col, self::action()->columns)) {
            return DB::table('users')->where($col, $val)->get();
        }
        return "Column not found";
    }

    public static function login($email, $password)
    {
        Validate::rules(['email' => $email], [
            'email' => 'required|email'
        ]);
        $user = self::get_by_email($email);
        if (isset($user[0]['password']) && password_verify($password, $user[0]['password'])) {
            Session::setSession('user', ['userId' => $user[0]['id'], 'userName' => $user[0]['name'], 'login' => 1]);
            Session::regenerate();
            return $user[0]['id'];
        } else if (!isset($user[0]['password'])) {
            return 'User Not exist';
        } else {
            return 'Password Is wrong';
        }
        return $user;
    }

    public static function authenticate()
    {
        if (isset($_POST) && count($_POST) > 0 && isset($_POST['signup'])) {
            $register = User::create(['name' => $_POST['name'], 'email' => $_POST['email'], 'password' => $_POST['password'], 'gender' => $_POST['gender'], 'phone' => $_POST['phone']]);
            return $register;
        }
        if (isset($_POST) && count($_POST) > 0 && isset($_POST['login'])) {
            $login = User::login($_POST['email'], $_POST['password']);
            return $login;
        }

        return null;
    }

    public static function auth()
    {
        return Session::getSession('user');
    }
}
