<?php

namespace Martijnvdb\PayhipProductOverview\Models;

use Martijnvdb\PayhipProductOverview\Models\Template;

class SettingField {

    private $id;
    private $title;
    private $type = 'text';
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

        if(isset($type)) {
            $this->type = $type;
        }

        return $this;
    }

    public static function create($id, $type = null)
    {
        return new self($id, $type);
    }

    public static function getItemValue($id)
    {
        return get_option($id);
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

    public function addOption($key, $value)
    {
        $this->options[] = [
            'key' => $key,
            'value' => $value,
            'selected' => false
        ];

        return $this;
    }

    public function getValue()
    {
        return self::getItemValue($this->id);

        return $value;
    }

    private function textSettingField()
    {
        return Template::build('SettingFields/text.html', [
            'id' => $this->id,
            'name' => $this->id,
            'label' => $this->label,
            'value' => esc_attr($this->getValue())
        ]);
    }

    private function textareaSettingField()
    {
        return Template::build('SettingFields/textarea.html', [
            'id' => $this->id,
            'name' => $this->id,
            'label' => $this->label,
            'value' => esc_attr($this->getValue())
        ]);
    }

    private function selectSettingField()
    {
        $value = $this->getValue($this->index);
        foreach($this->options as &$option) {
            if($option['key'] == $value) {
                $option['selected'] = true;
                break;
            }
        }

        return Template::build('SettingFields/select.html', [
            'id' => $this->id,
            'name' => $this->id,
            'label' => $this->label,
            'options' => $this->options,
            'value' => esc_attr($this->getValue())
        ]);
    }

    private function checkboxSettingField()
    {
        return Template::build('SettingFields/checkbox.html', [
            'id' => $this->id,
            'name' => $this->id,
            'label' => $this->label,
            'checked' => $this->getValue() ? 'checked' : ''
        ]);
    }


    private function numberSettingField()
    {
        return Template::build('SettingFields/number.html', [
            'id' => $this->id,
            'name' => $this->id,
            'label' => $this->label,
            'value' => esc_attr($this->getValue()),
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step
        ]);
    }

    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    public function build()
    {
        return $this->{"{$this->type}SettingField"}();
    }
}