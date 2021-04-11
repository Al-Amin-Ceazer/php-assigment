<?php

namespace App\Classes;

class Session
{
    private static $_session_status = false;

    public static function init()
    {
        if (self::$_session_status == false) {
            session_start();
            self::$_session_status = true;
        }
    }

    public static function set($key, $val)
    {
        $_SESSION[$key] = $val;
    }

    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return false;
        }
    }

    public static function destroy()
    {
        if (self::$_session_status == true) {
            session_destroy();
        }
    }
}
