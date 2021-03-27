<?php

/**
 * This file is part of the martijnvdb/wordpress-plugin-tools library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Martijn van den Bosch <martijn_van_den_bosch@hotmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Martijnvdb\WordpressPluginTools;

// Prevent direct access
if(!defined('ABSPATH')) {
    exit;
}

class Template {

    public static function build($file_path, $data = [])
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../resources/views');
        $twig = new \Twig\Environment($loader);
        
        return $twig->render($file_path, $data);
    }
}