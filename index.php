<?php

include_once('Bootstrap.php');

use Classes\Db;

// Select
// $table = DB::table('users')->orWhere('name', 'Shwe')->orWhere('phone', 23464)->get();
// echo "<pre>";
// var_dump($table);

// Insert Create
$table1 = DB::table('users')->create(['name' => 'ko myo', 'email' => 'komyo@gmail.com', 'password' => '', 'gender' => 1, 'phone' => 3434]);
var_dump($table1);

// Delete