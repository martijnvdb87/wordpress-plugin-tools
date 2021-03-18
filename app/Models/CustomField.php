<?php

namespace Martijnvdb\PayhipProductOverview\Models;

use Martijnvdb\PayhipProductOverview\Models\Template;

class CustomField {

    private $id;
    private $title;
    private $type;
    private $label;

    private $options = [];

    public function __construct($id)
    {
        $id = sanitize_key($id);
        $this->id = "mvdb-wp-custom-field-$id";
        
        add_action('save_post', [$this, 'save']);

        return $this;
    }

    public static function create($id)
    {
        return new self($id);
    }

    public function setType($value)
    {
        $this->type = $value;
        return $this;
    }

    public function setLabel($value)
    {
        $this->label = $value;
        return $this;
    }

    public function save()
    {
        global $post;
        if(empty($_POST)) return; 
        update_post_meta($post->ID, $this->id, $_POST[$this->id]);
    }

    public function addOption($key, $value)
    {
        $this->options[] = [
            'key' => $key,
            'value' => $value,
            'selected' => false
        ];

        return $this;
    }

    private function getValue()
    {
        global $post;
        $custom = get_post_custom($post->ID);
        $value = isset($custom[$this->id][0]) ? $custom[$this->id][0] : '';

        return $value;
    }

    private function textCustomField()
    {
        return Template::build('CustomFields/text.html', [
            'id' => $this->id,
            'label' => $this->label,
            'value' => $this->getValue()
        ]);
    }

    private function textareaCustomField()
    {
        return Template::build('CustomFields/textarea.html', [
            'id' => $this->id,
            'label' => $this->label,
            'value' => $this->getValue()
        ]);
    }

    private function selectCustomField()
    {
        $value = $this->getValue();
        foreach($this->options as &$option) {
            if($option['key'] == $value) {
                $option['selected'] = true;
                break;
            }
        }

        return Template::build('CustomFields/select.html', [
            'id' => $this->id,
            'label' => $this->label,
            'options' => $this->options
        ]);
    }

    private function checkboxCustomField()
    {
        return Template::build('CustomFields/checkbox.html', [
            'id' => $this->id,
            'label' => $this->label,
            'checked' => $this->getValue() ? 'checked' : ''
        ]);
    }

    public function build()
    {
        return $this->{"{$this->type}CustomField"}();
    }
}