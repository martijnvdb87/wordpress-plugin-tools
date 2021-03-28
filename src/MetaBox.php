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

use Martijnvdb\WordpressPluginTools\CustomField;

// Prevent direct access
if(!defined('ABSPATH')) {
    exit;
}

/**
 * MetaBox provides methods for working with metaboxes in Wordpress.
 */
class MetaBox {
    
    /**
     * The MetaBox id.
     * @var string
     */
    private $id;

    /**
     * The MetaBox title.
     * @var string
     */
    private $title = '';

    /**
     * An array of posttype ID's in which the MetaMox has to be shown.
     * @var array
     */
    private $post_types = [];

    /**
     * The context within the screen where the metabox should display.
     * @var string
     */
    private $context = 'normal';

    /**
     * The priority within the context where the box should show.
     * @var string
     */
    private $priority = 'default';

    /**
     * An array of CustomFields and Lists which should be shown in the MetaBox.
     * @var array
     */
    private $items = [];

    /**
     * An array of texts that are used in the Metabox template.
     * @var array
     */
    private $text = [
        'new' => 'New',
        'delete_confirm' => 'Are you sure you want to delete this item?',
    ];

    /**
     * Creates a new instance of the MetaBox class.
     * 
     * @param  string $id
     */
    public function __construct(string $id)
    {
        $id = sanitize_key($id);
        $this->id = "martijnvdb-wordpress-plugin-tools-metabox-$id";
        $this->title = $this->convertToLabel($id);
    }

    /**
     * Create a new instance of the MetaBox class.
     * 
     * @param  string $id
     * @return MetaBox
     */
    public static function create($id): MetaBox
    {
        return new self($id);
    }

    /**
     * Loads the MetaBox scripts.
     * 
     * @return void
     */
    public function loadScript(): void
    {
        wp_enqueue_script('martijnvdb-wordpress-plugin-tools-metabox-script', plugins_url( 'resources/js/metabox.js', __DIR__ . '/../../' ));
    }

    /**
     * Loads the MetaBox styles.
     * 
     * @return void
     */
    public function loadStyle(): void
    {
        wp_enqueue_style('martijnvdb-wordpress-plugin-tools-metabox-style', plugins_url( 'resources/css/metabox.css', __DIR__ . '/../../' ));
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
     * Set the title of the MetaBox.
     * 
     * @param  string $value
     * @return MetaBox
     */
    public function setTitle(string $value): MetaBox
    {
        $this->title = $value;

        return $this;
    }

    /**
     * Register the PostTypes in which the MetaBox should be shown.
     * 
     * @param  array $post_types
     * @return MetaBox
     */
    public function addPostTypes(array $post_types = []): MetaBox
    {
        foreach($post_types as $post_type) {
            $this->addPostType($post_type);
        }

        return $this;
    }

    /**
     * Add a PostType in which the MetaBox should be shown.
     * 
     * @param  string $post_type
     * @return MetaBox
     */
    public function addPostType(string $post_type): MetaBox
    {
        $this->post_types[] = $post_type;

        return $this;
    }

    /**
     * Register the MetaBox.
     * 
     * @return void
     */
    public function metaBox(): void
    {
        add_meta_box($this->id, $this->title, [$this, 'metaBoxContent'], $this->post_types, $this->context, $this->priority);
    }

    /**
     * The MetaBox content callback function.
     * 
     * @return void
     */
	public function metaBoxContent()
    {
        foreach($this->items as $item) {

            if($item['type'] == 'field') {
                foreach($item['fields'] as $field) {
                    echo $field->build();
                }
            }

            if($item['type'] == 'list') {
                $list_amount = 0;
                $fields = [];
                $lists = [];
                
                foreach($item['fields'] as $index => $field) {
                    $field_value = CustomField::getItemValue($field->getId());
                    $fields[] = $field->setIndex(0)->build();

                    if(is_array($field_value)) {
                        $field_size = sizeof($field_value);

                    } else {
                        $field_size = 0;
                    }

                    $list_amount = $list_amount < $field_size ? $field_size : $list_amount;
                }

                for($list_index = 0; $list_index < $list_amount; $list_index++) {
                    $fields = [];
                    foreach($item['fields'] as $field) {
                        $fields[] = $field->setIndex($list_index)->build();
                    }

                    $lists[] = [
                        'fields' => $fields
                    ];
                }

                echo Template::build('MetaBox/list.html', [
                    'id' => uniqid("{$this->id}-", true),
                    'label' => $item['label'],
                    'fields' => $fields,
                    'lists' => $lists,
                    'text' => $this->text
                ]);
            }
        }
	}

    /**
     * Set an array with texts.
     * 
     * @param  array $texts
     * @return MetaBox
     */
    public function setTexts(array $texts = []): MetaBox
    {
        foreach($texts as $key => $value) {
            $this->setText($key, $value);
        }

        return $this;
    }

    /**
     * Set a text.
     * 
     * @param  string $key
     * @param  string $value
     * @return MetaBox
     */
    public function setText($key, $value): MetaBox
    {
        $this->text[$key] = $value;

        return $this;
    }

    /**
     * Add a CustomField to a MetaBox.
     * 
     * @param  CustomField $custom_field
     * @return MetaBox
     */
    public function addCustomField(CustomField $custom_field): MetaBox
    {
        $this->items[] = [
            'type' => 'field',
            'fields' => [$custom_field]
        ];

        return $this;
    }

    /**
     * Add one or more CustomField to a MetaBox.
     * 
     * @param  array $custom_fields
     * @return MetaBox
     */
    public function addCustomFields(array $custom_fields): MetaBox
    {
        $custom_fields = is_array($custom_fields) ? $custom_fields : [$custom_fields];

        $custom_fields = array_filter($custom_fields, function($item) {
            return $item instanceof CustomField;
        });
        
        foreach($custom_fields as $custom_field) {
            $this->addItem($custom_field);
        }

        return $this;
    }

    /**
     * Add a list with CustomField to a MetaBox.
     * 
     * @param  string $label
     * @param  array $custom_fields
     * @return MetaBox
     */
    public function addList(string $label, array $custom_fields = []): MetaBox
    {
        $custom_fields = is_array($custom_fields) ? $custom_fields : [$custom_fields];

        $custom_fields = array_filter($custom_fields, function($item) {
            return $item instanceof CustomField;
        });
        
        $this->items[] = [
            'type' => 'list',
            'label' => $label,
            'fields' => $custom_fields
        ];

        return $this;
    }

    /**
     * Build the MetaBox.
     * 
     * @return void
     */
    public function build(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'loadScript']);
        add_action('admin_enqueue_scripts', [$this, 'loadStyle']);
        add_action('add_meta_boxes', [$this, 'metaBox']);
    }
}