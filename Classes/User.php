<?php

namespace Classes;

class User
{
    protected $instance;
    public function __construct()
    {
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
        return DB::table('users')->create($array);
    }
    public static function get($array)
    {
        // return DB::table('users')->where($key, $value);
    }
}
