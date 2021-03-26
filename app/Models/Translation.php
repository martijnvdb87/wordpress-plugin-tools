<?php

namespace Martijnvdb\WordpressPluginTools\Models;

use Martijnvdb\WordpressPluginTools\Models\Setting;

class Translation {
    private $dir;

    public static function get($key)
    {
        return __($key, Setting::get('text_domain'));
    }

    public static function echo($key)
    {
        echo self::get($key);
    }
}