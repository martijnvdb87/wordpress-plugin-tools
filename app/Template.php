<?php

namespace Martijnvdb\WordpressPluginTools;

class Template {

    public static function build($file_path, $data = [])
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/Views');
        $twig = new \Twig\Environment($loader);
        
        return $twig->render($file_path, $data);
    }
}