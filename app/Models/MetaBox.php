<?php

namespace Martijnvdb\PayhipProductOverview\Models;

use Martijnvdb\PayhipProductOverview\Models\CustomField;

class MetaBox {

    private $id;
    private $title = '';
    private $posttypes = [];
    private $context = 'normal';
    private $priority = 'default';

    private $items = [];
    private $custom_fields = [];

    public function __construct($id)
    {
        $id = sanitize_key($id);
        $this->id = "mvdb-wp-metabox-$id";
        $this->title = $this->convertToLabel($id);
        
        return $this;
    }

    public function loadScript()
    {
        wp_enqueue_script('mvdb-wp-metabox-script', plugins_url( 'assets/scripts/metabox.js', __DIR__ . '/../../../' ));
        wp_enqueue_script('mvdb-wp-sortable-script', plugins_url( 'assets/scripts/sortable.js', __DIR__ . '/../../../' ));
    }

    public function loadStyle()
    {
        wp_enqueue_style('mvdb-wp-metabox-style', plugins_url( 'assets/styles/metabox.css', __DIR__ . '/../../../' ));
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

            // TODO

            if($item['type'] == 'list') {
                $list_amount = 0;
                $lists = [];
                
                foreach($item['fields'] as $index => $field) {
                    $field_value = CustomField::getItemValue($field->getId());

                    if(is_array($field_value)) {
                        $field_size = sizeof($field_value);

                    } else {
                        $field_size = 1;
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
                    'id' => uniqid("{$this->id}-") . random_int(10000000, 99999999),
                    'lists' => $lists,
                    'translate' => [
                        'new' => Translation::get('New'),
                        'delete' => Translation::get('Delete'),
                        'delete_confirm' => Translation::get('Are you sure you want to delete this item?'),
                        'drag' => Translation::get('Drag'),
                    ]
                ]);
            }
        }
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

    public function addList($custom_fields = [], $max_lists = null)
    {
        if(!is_array($custom_fields)) {
            $custom_fields = [$custom_fields];
        }

        $custom_fields = array_filter($custom_fields, function($item) {
            return $item instanceof CustomField;
        });
        
        $this->items[] = [
            'type' => 'list',
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