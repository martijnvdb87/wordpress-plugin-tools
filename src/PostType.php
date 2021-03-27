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
 * PostType provides methods for working with post types in Wordpress.
 */
class PostType {

    /**
     * The PostType id.
     * @var string
     */
    private $id;

    /**
     * The MetaBoxes which should be shown in the PostType.
     * @var array
     */
    private $metaboxes = [];

    /**
     * The PostType options.
     * @var array
     */
    private $options = [
        'supports' => ['title', 'editor'],
        'labels' => [],
        'rewrite' => []
    ];

    /**
     * Creates a new instance of the PostType class.
     * 
     * @param  string $id
     */
    public function __construct($id)
    {
        $id = sanitize_key($id);
        $this->id = $id;
        $this->setLabel('name', $this->convertToLabel($id));
        $this->setSlug($id);
        
        return $this;
    }

    /**
     * Create a new instance of the PostType class.
     * 
     * @param  string $id
     * @return PostType
     */
    public static function create(string $id): PostType
    {
        return new self($id);
    }

    /**
     * Converts an ID into a label text.
     * 
     * @param  string $value
     * @return string
     */
    private function convertToLabel(string $value): string
    {
        $value = preg_replace('/[-_]/', ' ', $value);
        $value = ucwords($value);
        return $value;
    }

    /**
     * Add MetaBox to PostType.
     * 
     * @param  array $metaboxes
     * @return PostType
     */
    public function addMetaBox(array $metaboxes = []): PostType
    {
        $metaboxes = is_array($metaboxes) ? $metaboxes : [$metaboxes];

        foreach($metaboxes as $metabox) {
            $this->metaboxes[] = $metabox;
            $metabox->addPosttype($this->id);
        }

        return $this;
    }

    /**
     * Set a label of the PostType.
     * 
     * @param  string $key
     * @param  string $value
     * @return PostType
     */
    public function setLabel(string $key, string $value = ''): PostType
    {
        if($key == 'name') {
            $this->options['label'] = $value;
        }

        $this->options['labels'][$key] = $value;

        return $this;
    }

    /**
     * Set labels of the PostType.
     * 
     * @param  array $values
     * @return PostType
     */
    public function setLabels(array $values = []): PostType
    {
        foreach($values as $key => $value) {
            $this->setLabel($key, $value);
        }

        return $this;
    }

    /**
     * Set a description of the PostType.
     * 
     * @param  string $value
     * @return PostType
     */
    public function setDescription(string $value): PostType
    {
        $this->options['description'] = $value;
        
        return $this;
    }

    /**
     * Set the accessibility of the PostType to public.
     * 
     * @param  bool $value
     * @return PostType
     */
    public function setPublic(bool $value = true): PostType
    {
        $this->options['public'] = (boolean) $value;
        
        return $this;
    }

    /**
     * Allow the PostType to be searchable.
     * 
     * @param  bool $value
     * @return PostType
     */
    public function setSearchable(bool $value = true): PostType
    {
        $this->options['exclude_from_search'] = (boolean) !$value;
        
        return $this;
    }

    /**
     * Set the menu position of the PostType.
     * 
     * @param  bool $value
     * @return PostType
     */
    public function setMenuPosition(int $value): PostType
    {
        $this->options['show_in_menu'] = true;
        $this->options['menu_position'] = (int) $value;
        
        return $this;
    }

    /**
     * Set the menu icon of the PostType.
     * 
     * @param  string $value
     * @return PostType
     */
    public function setIcon(string $value): PostType
    {
        $this->options['show_in_menu'] = true;
        $this->options['menu_icon'] = (string) $value;
        
        return $this;
    }

    /**
     * Add support to the PostType.
     * 
     * @param  array $value
     * @return PostType
     */
    public function addSupport(array $values = []): PostType
    {
        $values = is_array($values) ? $values : [$values];

        foreach($values as $value) {
            if(!in_array($value, $this->options['supports'])) {
                $this->options['supports'][] = $value;
            }
        }
        
        return $this;
    }

    /**
     * Remove support from the PostType.
     * 
     * @param  array $value
     * @return PostType
     */
    public function removeSupport(array $values = []): PostType
    {
        $values = is_array($values) ? $values : [$values];
        
        foreach($values as $value) {
            $this->options['supports'] = array_filter($this->options['supports'], function($option) use ($value) {
                return $option != $value;
            });
        }
        
        return $this;
    }

    /**
     * Set the slug of the PostType.
     * 
     * @param  string $value
     * @return PostType
     */
    public function setSlug(string $value): PostType
    {
        $this->options['rewrite']['slug'] = $value;
        
        return $this;
    }

    /**
     * Add support for the block editor to the PostType.
     * 
     * @param  bool $value
     * @return PostType
     */
    public function addBlockEditor(bool $value = true): PostType
    {
        $this->options['show_in_rest'] = $value;
        
        return $this;
    }

    /**
     * Add any option supported by Wordpress to the PostType.
     * 
     * @param  string $key
     * @param  string $value
     * @return PostType
     */
    public function addOption(string $key, string $value): PostType
    {
        $this->options[$key] = $value;
        
        return $this;
    }

    /**
     * Register the PostType.
     * 
     * @return void
     */
    public function register(): void
    {
        register_post_type($this->id, $this->options);
    }

    /**
     * Build the PostType.
     * 
     * @return void
     */
    public function build(): void
    {
        $metaboxes = $this->metaboxes;
        foreach($metaboxes as $metabox) {
            $metabox->build();
        }
        add_action('init', [$this, 'register']);
    }
}
