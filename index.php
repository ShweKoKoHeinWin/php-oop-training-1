<?php

include_once('Bootstrap.php');

use Classes\Db;

// Select
// $table = DB::table('users')->orWhere('name', 'Shwe')->orWhere('phone', 23464)->get();
// echo "<pre>";
// var_dump($table);

// Insert Create
$table1 = DB::table('users')->where('name', 'ko myo')->orWhere('email', 'maaye@gmail.com')->orWhere('password', '')->destroy();
var_dump($table1);

// Delete