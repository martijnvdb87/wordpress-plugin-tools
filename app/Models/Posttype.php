<?php

namespace Martijnvdb\PayhipProductOverview\Models;

class PostType {

    private $id;
    private $options = [
        'supports' => ['title', 'editor'],
        'labels' => [],
        'rewrite' => []
    ];

    public function __construct($id)
    {
        $id = sanitize_key($id);
        $this->id = $id;
        $this->setLabel('name', $this->convertToLabel($id));
        $this->setSlug($id);
        
        return $this;
    }

    public static function create($id)
    {
        return new self($id);
    }

    private function convertToLabel($value)
    {
        $value = preg_replace('/[-_]/', ' ', $value);
        $value = ucwords($value);
        return $value;
    }

    public function setLabel($key, $value = '')
    {
        if($key == 'name') {
            $this->options['label'] = $value;
        }

        $this->options['labels'][$key] = $value;

        return $this;
    }

    public function setLabels($values = [])
    {
        foreach($values as $key => $value) {
            $this->setLabel($key, $value);
        }

        return $this;
    }

    public function setDescription($value)
    {
        $this->options['description'] = $value;
        
        return $this;
    }

    public function setPublic($value = true)
    {
        $this->options['public'] = (boolean) $value;
        
        return $this;
    }

    public function setHierarchical($value = true)
    {
        $this->options['hierarchical'] = (boolean) $value;
        
        return $this;
    }

    public function setSearchable($value = true)
    {
        $this->options['exclude_from_search'] = (boolean) !$value;
        
        return $this;
    }

    public function setQueryable($value = true)
    {
        $this->options['publicly_queryable'] = (boolean) $value;
        
        return $this;
    }

    public function setMenuPosition($value)
    {
        $this->options['show_in_menu'] = true;
        $this->options['menu_position'] = (int) $value;
        
        return $this;
    }

    public function setMenuIcon($value)
    {
        $this->options['show_in_menu'] = true;
        $this->options['menu_position'] = (string) $value;
        
        return $this;
    }

    public function addSupport($values = [])
    {
        $values = is_array($values) ? $values : [$values];

        foreach($values as $value) {
            if(!in_array($value, $this->options['supports'])) {
                $this->options['supports'][] = $value;
            }
        }
        
        return $this;
    }

    public function removeSupport($values = [])
    {
        $values = is_array($values) ? $values : [$values];
        
        foreach($values as $value) {
            $this->options['supports'] = array_filter($this->options['supports'], function($option) use ($value) {
                return $option != $value;
            });
        }
        
        return $this;
    }

    public function setSlug($value)
    {
        $this->options['rewrite']['slug'] = $value;
        
        return $this;
    }

    public function addShowInRest($value = true)
    {
        $this->options['show_in_rest'] = $value;
        
        return $this;
    }

    public function addOption($key, $value)
    {
        $this->options[$key] = $value;
        
        return $this;
    }

    public function register()
    {
        register_post_type($this->id, $this->options);
    }

    public function build()
    {
        add_action('init', [$this, 'register']);
    }
}