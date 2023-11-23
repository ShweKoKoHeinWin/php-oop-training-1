<?php

include_once('Bootstrap.php');

use Classes\Db;

// Select
// $table = DB::table('users')->orWhere('name', 'Shwe')->orWhere('phone', 23464)->get();
// echo "<pre>";
// var_dump($table);

// Insert Create
$table1 = DB::table('users')->create(['name' => 'Ma aye', 'email' => 'maaye@gmail.com', 'password' => 'maaye1234', 'gender' => 2, 'phone' => 8998]);

// Delete