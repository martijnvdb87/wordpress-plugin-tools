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

use Martijnvdb\WordpressPluginTools\Template;

// Prevent direct access
if(!defined('ABSPATH')) {
    exit;
}

/**
 * CustomFields provides methods for working with custom fields in Wordpress.
 */
class CustomField {

    /**
     * The CustomField id.
     * @var string
     */
    private $id;

    /**
     * The CustomField title.
     * @var string
     */
    private $title;

    /**
     * The CustomField type.
     * @var string
     */
    private $type = 'text';

    /**
     * The type of the page where the CustomField will be shown.
     * @var string
     */
    private $page_type = 'posttype';

    /**
     * The current index of the value array.
     * @var int
     */
    private $index;
    
    /**
     * The CustomField label.
     * @var string
     */
    private $label;
    
    /**
     * The minimal value possible if the CustomField type is a 'number' type.
     * @var int
     */
    private $min;

    /**
     * The maximal value possible if the CustomField type is a 'number' type.
     * @var int
     */
    private $max;

    /**
     * The size of the steps if the CustomField type is a 'number' type.
     * @var int
     */
    private $step;
    
    /**
     * All the posible options if the CustomField type is a 'select' or 'radio' type.
     * @var array
     */
    private $options = [];

    /**
     * Creates a new instance of the CustomField class and register the 'save_post' action.
     * 
     * @param  string $id
     */
    public function __construct(string $id, ?string $type = null)
    {
        $id = sanitize_key($id);
        $this->id = $id;
        
        add_action('save_post', [$this, 'save']);
    }

    /**
     * Create a new instance of the CustomField class.
     * 
     * @param  string $id
     * @return CustomField
     */
    public static function create(string $id): CustomField
    {
        return new self($id);
    }

    /**
     * Get the value of an item in a PostType.
     * 
     * @param  string $id
     * @param  null|int $index
     * @return mixed
     */
    public static function getItemValue(string $id, ?int $index = null)
    {
        global $post;

        $value = '';
        
        $post_meta = get_post_meta($post->ID, $id, true);
        
        if(is_array($post_meta) && isset($index)) {
            $value = isset($post_meta[$index]) ? $post_meta[$index] : null;

        } else {
            $value = $post_meta;
        }

        return $value;
    }

    /**
     * Get the value of a Settings item.
     * 
     * @param  string $id
     * @return mixed
     */
    public static function getSettingValue(string $id)
    {
        return get_option($id);
    }

    /**
     * Set the type of the page in which the CustomField is shown.
     * 
     * @param  string $page_type
     * @return CustomField
     */
    public function setPageType(string $page_type): CustomField
    {
        $this->page_type = $page_type;

        return $this;
    }

    /**
     * Get the ID of the current CustomField.
     * 
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set the type of the CustomField.
     * 
     * @param  string $value
     * @return CustomField
     */
    public function setType(string $value): CustomField
    {
        $this->type = $value;

        return $this;
    }

    /**
     * Set the label of the CustomField.
     * 
     * @param  string $value
     * @return CustomField
     */
    public function setLabel(string $value): CustomField
    {
        $this->label = $value;

        return $this;
    }

    /**
     * Set the minimal value of the CustomField (only used if CustomField is type 'number').
     * 
     * @param  int $value
     * @return CustomField
     */
    public function setMin(int $value): CustomField
    {
        $this->min = $value;

        return $this;
    }

    /**
     * Set the maximal value of the CustomField (only used if CustomField is type 'number').
     * 
     * @param  int $value
     * @return CustomField
     */
    public function setMax(int $value): CustomField
    {
        $this->max = $value;

        return $this;
    }

    /**
     * Set the size of the steps of the CustomField (only used if CustomField is type 'number').
     * 
     * @param  int $value
     * @return CustomField
     */
    public function setStep(int $value): CustomField
    {
        $this->step = $value;

        return $this;
    }

    /**
     * Save the post.
     * 
     * @return void
     */
    public function save(): void
    {
        global $post;
        if(empty($_POST) || !isset($_POST[$this->id])) {
            return;
        }

        update_post_meta($post->ID, $this->id, $_POST[$this->id]);
    }

    /**
     * Set multiple options for a 'select' or 'radio' CustomField.
     * 
     * @param  array $options
     * @return CustomField
     */
    public function addOptions(array $options = []): CustomField
    {
        foreach($options as $key => $value) {
            $this->addOption($key, $value);
        }

        return $this;
    }

    /**
     * Set an option for a 'select' or 'radio' CustomField.
     * 
     * @param  string $key
     * @param  string $value
     * @return CustomField
     */
    public function addOption(string $key, string $value): CustomField
    {
        $this->options[] = [
            'key' => $key,
            'value' => $value,
            'selected' => false
        ];

        return $this;
    }

    /**
     * Get the value of the current CustomField.
     * 
     * @param  null|int $index
     * @return callable
     */
    public function getValue(?int $index = null)
    {
        if($this->page_type == 'setting') {
            return self::getSettingValue($this->id);
        }

        return self::getItemValue($this->id, $index);
    }

    /**
     * Build CustomField type 'text'.
     * 
     * @return string
     */
    private function textCustomField(): string
    {
        return Template::build($this->getTemplatePath('text.html'), [
            'id' => uniqid("{$this->id}-", true),
            'name' => $this->id,
            'label' => $this->label,
            'value' => $this->getValue($this->index),
            'index' => isset($this->index) ? $this->index : false
        ]);
    }

    /**
     * Build CustomField type 'textarea'.
     * 
     * @return string
     */
    private function textareaCustomField(): string
    {
        return Template::build($this->getTemplatePath('textarea.html'), [
            'id' => uniqid("{$this->id}-", true),
            'name' => $this->id,
            'label' => $this->label,
            'value' => $this->getValue($this->index),
            'index' => isset($this->index) ? $this->index : false
        ]);
    }

    /**
     * Build CustomField type 'select'.
     * 
     * @return string
     */
    private function selectCustomField(): string
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
    
    /**
     * Build CustomField type 'radio'.
     * 
     * @return string
     */
    private function radioCustomField(): string
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

    /**
     * Build CustomField type 'checkbox'.
     * 
     * @return string
     */
    private function checkboxCustomField(): string
    {
        return Template::build($this->getTemplatePath('checkbox.html'), [
            'id' => uniqid("{$this->id}-", true),
            'name' => $this->id,
            'label' => $this->label,
            'checked' => $this->getValue($this->index) ? 'checked' : '',
            'index' => isset($this->index) ? $this->index : false
        ]);
    }

    /**
     * Build CustomField type 'number'.
     * 
     * @return string
     */
    private function numberCustomField(): string
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

    /**
     * Build CustomField type 'editor'.
     * 
     * @return string
     */
    private function editorCustomField(): ?string
    {
        if(isset($this->index)) {
            return null;
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

    /**
     * Generate template path.
     * 
     * @param  string $path
     * @return string
     */
    private function getTemplatePath(string $path): string
    {
        if($this->page_type == 'setting') {
            return "SettingFields/$path";
        }
        
        return "CustomFields/$path";
    }

    /**
     * Set the index of the value array.
     * 
     * @param  int $index
     * @return CustomField
     */
    public function setIndex(int $index): CustomField
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Build the CustomField.
     *
     * @return string
     */
    public function build(): string
    {
        return $this->{"{$this->type}CustomField"}();
    }
}