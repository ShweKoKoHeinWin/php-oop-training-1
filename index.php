<?php

include_once('Bootstrap.php');


use Classes\Db;
use Classes\User;
use Classes\Validate;
use Classes\Session;
use Classes\Route;

Session::startSession();

// $create1 = User::create(['name' => 'Shwe Ko', 'email' => 'shweko@gmail.com', 'password' => 'shweko2003', 'gender' => 1, 'phone' => '3333']);
// var_dump($create1);
// echo '<hr>';

// $get1 = User::get_by_name('aaaa');
// var_dump($get1);

// $create2 = User::create(['id' => 4, 'name' => 'Nyune Lay', 'email' => 'nyune@gmail.com', 'password' => 'nyunelay', 'gender' => 2, 'phone' => '44']);
// var_dump($create2);
// echo '<hr>';

// $get2 = User::get_by_id(4);
// var_dump($get2);

// $create3 = User::create(['password' => 'shweko2003', 'gender' => 1, 'phone' => '3333']);
// var_dump($create3);
// echo '<hr>';

// $get3 = User::get_by_email('bbbb@gmail.com');
// var_dump($get3);
// echo '<hr>';

// $find = User::find(2);
// var_dump($find);
// echo '<hr>';

// $db = Db::table('users')->where('name', 'Shwe Ko')->get();
// var_dump($db);
// echo '<hr>';

// $dbdelete1 = Db::table('users')->where('name', 'Shwe Ko')->destroy();
// var_dump($dbdelete1);
// echo '<hr>';

// $dbdelete2 = Db::table('users')->where('name', 'Shwe Ko')->orWhere('name', 'aaaa')->destroy();
// var_dump($dbdelete2);
// echo '<hr>';

// $dbUpdate = Db::table('users')->where('gender', 1)->update(['phone' => 0]);
// var_dump($dbUpdate);
// echo '<hr>';
// $validate = Validate::rules(['name' => '', 'email' => 'shweko@gmail.'], ['name' => 'required|max:3', 'email' => 'required|email']);
// var_dump(Validate::verify('nyunelay', $get2[0]['password']));
// var_dump(password_verify('hsushoon',  password_hash('hsushoon', PASSWORD_DEFAULT)));
// var_dump(password_hash('hsushoon', PASSWORD_DEFAULT), $get2[0]['password']);

$errors = Validate::getErrors();
var_dump($_SESSION);
?>

<?php
echo !User::auth() ? "<h3><a href='index.php?route=login'>login</a></h3>" : "";
?>
<?php
echo !User::auth() ? "<h3><a href='index.php?route=signup'>signup</a></h3>" : "";
?>
<?php
echo User::auth() ? "<h3><a href='index.php?route=logout'>logout</a></h3>" : "";
?>


<?php
Route::controller();
