<?php

include_once('Bootstrap.php');

use Classes\Db;
use Classes\User;
// Select
// $table = DB::table('users')->orWhere('name', 'Shwe')->orWhere('phone', 23464)->get();
echo "<pre>";
// var_dump($table);

// Insert Create
// $table1 = DB::table('users')->where('name', 'ko myo')->orWhere('email', 'maaye@gmail.com')->orWhere('password', '')->destroy();

// $table1 = DB::table('users')->find(3);
// echo "<hr>";
// $table2 = DB::table('users')->find(6);
// echo "<hr>";
// $table3 = DB::table('users')->find(7);
// var_dump($table1);
// echo "<hr>";
// var_dump($table2);
// echo "<hr>";
// var_dump($table3);
// echo "<hr>";
// var_dump($table3->destroy());
// echo "<pre>";
$select1 = User::find(2)->get();
// $select2 = DB::table('users')->where('id', 1)->orWhere('gender', 1)->update(['phone' => 999]);
// $select3 = User::where('name', 'Ma mi')->orWhere('name', 'Ma bu')->get(['phone' => 55]);
// echo "<hr>";
// $select2 = DB::table('users')->where('name', 'aaaa')->get();
// $select3 = DB::table('users')->where('name', 'Ma mi')->get();
// // echo "<hr>";
// $create1 = User::create(['id' => 2, 'name' => 'aaaa', 'email' => 'aaaa@gmail.com', 'password' => 'aaaa2003', 'phone' => 34534, 'gender' => 1]);
// // echo "<hr>";
// $create2 = DB::table('users')->create(['id' => 30, 'name' => 'ppp', 'email' => 'ppp@gmail.com', 'password' => 'ppp2003', 'phone' => 34534, 'gender' => 1]);
// echo "<hr>";
// $create3 = DB::table('users')->create(['id' => 1, 'name' => '', 'email' => 'bbbb@gmail.com', 'password' => '', 'phone' => 34534, 'gender' => 1]);
// echo "<hr>";
// $create4 = DB::table('users')->create(['id' => 1, 'name' => 'bbbb', 'email' => 'bbbb@gmail.com', 'password' => 'bbbb2003', 'gender' => 1, 'phone' => 34534]);
// echo "<pre>";
// $delete1 = DB::table('users')->find(3)->destroy();
// echo '<hr>';
// $delete2 = DB::table('users')->where('id', 3)->get();
// $delete2 = DB::table('users')->where('name', 'bbbb')->orWhere('id', 7)->destroy();
// echo "<hr> one";
// var_dump($create1);
// var_dump($select1->destroy());
echo "<hr> two";
var_dump($select1);
// echo "<hr> create";
// echo "<hr> one";
// var_dump($create1);
// echo "<hr> two";
// var_dump($create2);
echo "<hr> three";
// var_dump($select3);
// echo "<hr> four";
// var_dump($create4);
// // echo "<hr>";
// var_dump($delete1);
// echo "<hr>";
// var_dump($delete2);
// var_dump($delete2);
// Delete