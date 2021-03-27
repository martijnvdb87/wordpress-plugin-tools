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

declare(strict_types = 1);

namespace Martijnvdb\WordpressPluginTools;

// Prevent direct access
if(!defined('ABSPATH')) {
    exit;
}

/**
 * Template provides methods for working with post types in Wordpress.
 */
class Template {

    /**
     * Build the Template.
     * 
     * @return string
     */
    public static function build(string $file_path, array $data = []): string
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../resources/views');
        $twig = new \Twig\Environment($loader);
        
        return $twig->render($file_path, $data);
    }
}