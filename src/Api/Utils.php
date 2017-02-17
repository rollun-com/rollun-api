<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 01.02.17
 * Time: 17:50
 */

namespace rollun\api\Api\Google;

class Utils
{
    public static function convertGmailToFilename($gmailAddress)
    {
        $str = str_replace('@gmail.com', '_at_gmail_dat_com', $gmailAddress);
        $str = str_replace('.', '', $str); //a.b@gmail.com and ab@gmail.com is same
        return static::convertStringToFilename($str);
    }

    public static function convertStringToFilename($str)
    {
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        $str = strtolower($str);
        $str = html_entity_decode($str, ENT_QUOTES, "utf-8");
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '-', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '-', $str);
        return $str;
    }
}
