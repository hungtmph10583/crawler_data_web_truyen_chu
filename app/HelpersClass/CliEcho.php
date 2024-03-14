<?php

namespace App\HelpersClass;



class CliEcho
{
    const WARNING = 'warning';
    const INFO = 'info';
    const SUCCESS = 'success';
    const ERROR = 'error';

    private static $flush_enable = false;

    public static function enable_flush($enabled = null)
    {
        if ($enabled !== null && !app()->runningInConsole())
        {
            self::$flush_enable = (bool)$enabled;
        }
        return self::$flush_enable;
    }

    public static function comment($message, $type = 'info', $newline = false, $return = false)
    {
        if (self::$flush_enable && ob_set_level() == 0)
        {
            ob_start();
        }

        if (app()->runningInConsole())
        {
            list($out, $end) = self::getCliOE($type);
        } else {
            list($out, $end) = self::getWebOE($type);
        }

        $out = $out . $message . $end;
        $out = "$out" . "\n";

        if ($return)
        {
            return $out;
        } else {
            echo $out;
            if (self::$flush_enable)
            {
                ob_flush();
                flush();
            }
        }
    }

    public static function info($message, $return = false) {return self::comment($message, 'info', false, $return);}
    public static function infonl($message, $return = false) {return self::comment($message, 'info', false, $return);}
    public static function success($message, $return = false) {return self::comment($message, 'success', false, $return);}
    public static function successnl($message, $return = false) {return self::comment($message, 'success', false, $return);}
    public static function warning($message, $return = false) {return self::comment($message, 'warning', false, $return);}
    public static function warningnl($message, $return = false) {return self::comment($message, 'warning', false, $return);}
    public static function error($message, $return = false) {return self::comment($message, 'error', false, $return);}
    public static function errornl($message, $return = false) {return self::comment($message, 'error', false, $return);}

    private static function getCliOE($type)
    {
        switch ($type) {
            case "success":
                $out = "\033[32m"; //Green background
                $end = "\033[0m";
                break;
            case "error":
                $out = "\033[31m"; //Red background
                $end = "\033[0m";
                break;
            case "warning":
                $out = "\033[33m"; //Yellow background
                $end = "\033[0m";
                break;
            case "info":
                $out = ""; //Blue background
                $end = "";
                break;
            default:
                throw new \Exception("Invalid status: " . $type);
        }
        return [$out, $end];
    }

    private static function getWebOE($type)
    {
        switch ($type) {
            case "success":
                $out = "<p style='color: green;'>"; //Green background
                $end = "</p>";
                break;
            case "error":
                $out = "<p style='color: red;'></p>"; //Red background
                $end = "</p>";
                break;
            case "warning":
                $out = "<p style='color: yellow;'></p>"; //Yellow background
                $end = "</p>";
                break;
            case "info":
                $out = "<p style='color: blue;'></p>"; //Blue background
                $end = "</p>";
                break;
            default:
                throw new \Exception("Invalid status: " . $type);
        }
        return [$out, $end];
    }
}