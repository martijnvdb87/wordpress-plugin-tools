<?php

namespace Martijnvdb\PayhipProductOverview\Models;

use Martijnvdb\PayhipProductOverview\Models\CustomField;

class MetaBox {

    private $id;
    private $title = '';
    private $posttypes = [];
    private $context = 'normal';
    private $priority = 'default';

    private $custom_fields = [];

    public function __construct($id)
    {
        $id = sanitize_key($id);
        $this->id = "mvdb-wp-metabox-$id";
        $this->title = $this->convertToLabel($id);
        
        return $this;
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
        foreach($this->custom_fields as $custom_field) {
            echo $custom_field->build();
        }
	}

    public function addCustomField($custom_field)
    {
        if($custom_field instanceof CustomField) {
            $this->custom_fields[] = $custom_field;
        }

        return $this;
    }

    public function build()
    {
        add_action('add_meta_boxes', [$this, 'metaBox']);
    }
}