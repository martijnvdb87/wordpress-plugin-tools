<?php

namespace Martijnvdb\PayhipProductOverview\Models;

use Martijnvdb\PayhipProductOverview\Models\Template;

class CustomField {

    private $id;
    private $title;
    private $type = 'text';
    private $index;
    private $label;

    private $options = [];

    public function __construct($id)
    {
        $id = sanitize_key($id);
        $this->id = $id;
        
        add_action('save_post', [$this, 'save']);

        return $this;
    }

    public static function create($id)
    {
        return new self($id);
    }

    public static function getItemValue($id, $index = null)
    {
        global $post;

        $value = '';
        
        $post_meta = get_post_meta($post->ID, $id, true);
        
        if(is_array($post_meta) && isset($index)) {
            $value = $post_meta[$index];

        } else {
            $value = $post_meta;
        }

        return $value;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getValueArraySize()
    {
        $post_meta = get_post_meta($post->ID, $id, false);

        print_r($post_meta);

        exit();
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

    public function getValue($index = null)
    {
        return self::getItemValue($this->id, $index);

        // exit();

        // if(isset($this->index)) {
        //     $custom = get_post_meta($post->ID, $this->id, false);

        //     if(is_array($custom)) {
        //         $value = isset($custom[0][$this->index]) ? $custom[0][$this->index] : '';
        //     }

        // } else {
        //     $value = get_post_meta($post->ID, $this->id, true);
        // }

        return $value;
    }

    private function textCustomField()
    {
        return Template::build('CustomFields/text.html', [
            'id' => $this->id,
            'label' => $this->label,
            'value' => $this->getValue($this->index),
            'is_array' => isset($this->index)
        ]);
    }

    private function textareaCustomField()
    {
        return Template::build('CustomFields/textarea.html', [
            'id' => $this->id,
            'label' => $this->label,
            'value' => $this->getValue($this->index),
            'is_array' => isset($this->index)
        ]);
    }

    private function selectCustomField()
    {
        $value = $this->getValue($this->index);
        foreach($this->options as &$option) {
            if($option['key'] == $value) {
                $option['selected'] = true;
                break;
            }
        }

        return Template::build('CustomFields/select.html', [
            'id' => $this->id,
            'label' => $this->label,
            'options' => $this->options,
            'is_array' => isset($this->index)
        ]);
    }

    private function checkboxCustomField()
    {
        return Template::build('CustomFields/checkbox.html', [
            'id' => $this->id,
            'label' => $this->label,
            'checked' => $this->getValue($this->index) ? 'checked' : '',
            'is_array' => isset($this->index)
        ]);
    }

    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    public function build()
    {
        return $this->{"{$this->type}CustomField"}();
    }
}