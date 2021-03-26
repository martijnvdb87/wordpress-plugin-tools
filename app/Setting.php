<?php

namespace Martijnvdb\WordpressPluginTools;

class Setting {
    private static $config;

    private static function parseConfig()
    {
        if(!isset(self::$config)) {
            self::$config = parse_ini_file(__DIR__ . '/../config.ini');
        }
    }

    public static function get($key)
    {
        self::parseConfig();
        return isset(self::$config[$key]) ? self::$config[$key] : null;
    }
}