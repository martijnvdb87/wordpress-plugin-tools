<?php

namespace Martijnvdb\WordpressPluginTools;

use Martijnvdb\WordpressPluginTools\CustomField;

class MetaBox {

    private $id;
    private $title = '';
    private $posttypes = [];
    private $context = 'normal';
    private $priority = 'default';

    private $items = [];
    private $custom_fields = [];
    private $text = [
        'new' => 'New',
        'delete_confirm' => 'Are you sure you want to delete this item?',
    ];

    public function __construct($id)
    {
        $id = sanitize_key($id);
        $this->id = "martijnvdb-wordpress-tools-metabox-$id";
        $this->title = $this->convertToLabel($id);
        
        return $this;
    }

    public function loadScript()
    {
        wp_enqueue_script('martijnvdb-wordpress-tools-metabox-script', plugins_url( 'assets/scripts/metabox.js', __DIR__ . '/../../' ));
    }

    public function loadStyle()
    {
        wp_enqueue_style('martijnvdb-wordpress-tools-metabox-style', plugins_url( 'assets/styles/metabox.css', __DIR__ . '/../../' ));
    }

    private function convertToLabel($value)
    {
        $value = preg_replace('/[-_]/', ' ', $value);
        $value = ucwords($value);
        return $value;
    }

    public static function create($id)
    {
        return new self($id);
    }

    public function setPosttype($posttypes = [])
    {
        if(!is_array($posttypes)) {
            $posttypes = [$posttypes];
        }

        $this->posttypes = $posttypes;

        return $this;
    }

    public function addPosttype($posttype)
    {
        $this->posttypes[] = $posttype;

        return $this;
    }

    public function metaBox()
    {
        add_meta_box($this->id, $this->title, [$this, 'metaBoxContent'], $this->posttypes, $this->context, $this->priority);
    }

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

    public function setTexts($texts = [])
    {
        foreach($texts as $key => $value) {
            $this->setText($key, $value);
        }

        return $this;
    }

    public function setText($key, $value)
    {
        $this->text[$key] = $value;

        return $this;
    }

    public function addItem($custom_fields = [])
    {
        if(!is_array($custom_fields)) {
            $custom_fields = [$custom_fields];
        }

        $custom_fields = array_filter($custom_fields, function($item) {
            return $item instanceof CustomField;
        });
        
        $this->items[] = [
            'type' => 'field',
            'fields' => $custom_fields
        ];

        return $this;
    }

    public function addList($label, $custom_fields = [], $max_lists = null)
    {
        if(!is_array($custom_fields)) {
            $custom_fields = [$custom_fields];
        }

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

    public function build()
    {
        add_action('admin_enqueue_scripts', [$this, 'loadScript']);
        add_action('admin_enqueue_scripts', [$this, 'loadStyle']);
        add_action('add_meta_boxes', [$this, 'metaBox']);
    }
}