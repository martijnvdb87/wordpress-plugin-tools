<?php

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