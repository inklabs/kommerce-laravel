<?php
namespace App\Lib;

class Arr
{
    public static function get($array, $key, $default = NULL)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }
}
