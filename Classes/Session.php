<?php

namespace Classes;

class Session
{
    public static function startSession()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
    }
    public static function setSession($key, $value)
    {
        self::startSession();
        if (is_array($value)) {
            foreach ($value as $idx => $prop) {
                if (is_numeric($idx)) {
                    $_SESSION[$key] = $value;
                } else {
                    $_SESSION[$key][$idx] = $prop;
                }
            }
        } else {
            $_SESSION[$key] = $value;
        }
    }

    public static function getSession($key, $key1 = null)
    {
        self::startSession();
        if (isset($_SESSION[$key]) && isset($_SESSION[$key1])) {
            return $_SESSION[$key][$key1];
        } else if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return null;
    }

    public static function unset($key)
    {
        self::startSession();
        if (self::getSession($key)) {
            unset($_SESSION[$key]);
        }
    }

    public static function flush()
    {
        self::startSession();
        session_destroy();
    }

    public static function regenerate()
    {
        session_regenerate_id(true);
    }
}
