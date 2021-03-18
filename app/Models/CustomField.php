<?php

namespace Martijnvdb\PayhipProductOverview\Models;

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
        $this->options[] = [$key, $value];

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
        $value = htmlentities($this->getValue());

        $output = "<section>";
        $output .= "<p><label for=\"{$this->id}\">{$this->label}</label></p>";
        $output .= "<p><input type=\"text\" id=\"{$this->id}\" name=\"{$this->id}\" value=\"$value\"></p>";
        $output .= "</section>";
        return $output;
    }

    private function textareaCustomField()
    {
        $value = htmlentities($this->getValue());

        $output = "<section>";
        $output .= "<p><label for=\"{$this->id}\">{$this->label}</label></p>";
        $output .= "<p><textarea id=\"{$this->id}\" name=\"{$this->id}\" class=\"large-text\" rows=\"8\">$value</textarea></p>";
        $output .= "</section>";
        return $output;
    }

    private function selectCustomField()
    {
        $value = $this->getValue();

        $output = "<section>";
        $output .= "<p><label for=\"{$this->id}\">{$this->label}</label></p>";
        $output .= "<select id=\"{$this->id}\" name=\"{$this->id}\">";

        foreach($this->options as $option) {
            $selected = $value === $option[0] ? ' selected' : '';
            $output .= "<option value=\"{$option[0]}\"$selected>{$option[1]}</option>";
        }

        $output .= "</select>";
        $output .= "</section>";
        return $output;
    }

    private function checkboxCustomField()
    {
        $value = $this->getValue();
        $checked = $this->getValue() ? ' checked' : '';

        $output = "<section>";
        $output = "<p>";
        $output .= "<label for=\"{$this->id}\">";
        $output .= "<input type=\"checkbox\" id=\"{$this->id}\" name=\"{$this->id}\" value=\"1\"$checked>";
        $output .= "{$this->label}";
        $output .= "</label>";
        $output .= "</p>";
        $output .= "</section>";
        return $output;
    }

    public function build()
    {
        return $this->{"{$this->type}CustomField"}();
    }
}