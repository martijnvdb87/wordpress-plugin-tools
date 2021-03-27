<?php

namespace Martijnvdb\WordpressPluginTools;

use Martijnvdb\WordpressPluginTools\Template;

class CustomField {

    private $id;
    private $title;
    private $type = 'text';
    private $page_type = 'posttype';
    private $index;
    private $label;
    private $min;
    private $max;
    private $step;

    private $options = [];

    public function __construct($id, $type = null)
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

    public static function getSettingValue($id)
    {
        return get_option($id);
    }

    public function setPageType($page_type)
    {
        $this->page_type = $page_type;

        return $this;
    }

    public function getSettingItemValue()
    {
        return get_option($this->id);
    }
    
    public function getId()
    {
        return $this->id;
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

    public function setMin($value)
    {
        $this->min = $value;
        return $this;
    }

    public function setMax($value)
    {
        $this->max = $value;
        return $this;
    }

    public function setStep($value)
    {
        $this->step = $value;
        return $this;
    }

    public function save()
    {
        global $post;
        if(empty($_POST)) return;

        update_post_meta($post->ID, $this->id, $_POST[$this->id]);
    }

    public function addOptions($options = [])
    {
        foreach($options as $key => $value) {
            $this->addOption($key, $value);
        }

        return $this;
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
        if($this->page_type = 'setting') {
            return self::getSettingValue($this->id);
        }

        return self::getItemValue($this->id, $index);
    }

    private function textCustomField()
    {
        return Template::build($this->getTemplatePath('text.html'), [
            'id' => uniqid("{$this->id}-", true),
            'name' => $this->id,
            'label' => $this->label,
            'value' => $this->getValue($this->index),
            'index' => isset($this->index) ? $this->index : false
        ]);
    }

    private function textareaCustomField()
    {
        return Template::build($this->getTemplatePath('textarea.html'), [
            'id' => uniqid("{$this->id}-", true),
            'name' => $this->id,
            'label' => $this->label,
            'value' => $this->getValue($this->index),
            'index' => isset($this->index) ? $this->index : false
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

        return Template::build($this->getTemplatePath('select.html'), [
            'id' => uniqid("{$this->id}-", true),
            'name' => $this->id,
            'label' => $this->label,
            'options' => $this->options,
            'index' => isset($this->index) ? $this->index : false
        ]);
    }
    
    private function radioCustomField()
    {
        $value = $this->getValue($this->index);
        foreach($this->options as &$option) {
            if($option['key'] == $value) {
                $option['checked'] = true;
                break;
            }
        }

        return Template::build($this->getTemplatePath('radio.html'), [
            'id' => uniqid("{$this->id}-", true),
            'name' => $this->id,
            'label' => $this->label,
            'options' => $this->options,
            'index' => isset($this->index) ? $this->index : false
        ]);
    }

    private function checkboxCustomField()
    {
        return Template::build($this->getTemplatePath('checkbox.html'), [
            'id' => uniqid("{$this->id}-", true),
            'name' => $this->id,
            'label' => $this->label,
            'checked' => $this->getValue($this->index) ? 'checked' : '',
            'index' => isset($this->index) ? $this->index : false
        ]);
    }

    private function numberCustomField()
    {
        return Template::build($this->getTemplatePath('number.html'), [
            'id' => uniqid("{$this->id}-", true),
            'name' => $this->id,
            'label' => $this->label,
            'value' => $this->getValue($this->index),
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step,
            'index' => isset($this->index) ? $this->index : false
        ]);
    }

    private function editorCustomField()
    {
        if(isset($this->index)) {
            return;
        }

        ob_start(); 
        wp_editor(htmlspecialchars_decode($this->getValue()), $this->id);
        $editor = ob_get_contents();
        ob_end_clean();

        return Template::build($this->getTemplatePath('editor.html'), [
            'label' => $this->label,
            'editor' => $editor
        ]);
    }

    private function getTemplatePath($path)
    {
        if($this->page_type == 'setting') {
            return "SettingFields/$path";
        }
        
        return "CustomFields/$path";
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