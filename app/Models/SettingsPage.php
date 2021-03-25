<?php

namespace Martijnvdb\PayhipProductOverview\Models;

class SettingsPage {

    private $id;
    private $page_title;
    private $menu_title;
    private $capability = 'administrator';
    private $slug;
    private $icon;
    private $items = [];

    public function __construct($id, $page_title, $menu_title)
    {
        $id = sanitize_key($id);
        $this->id = $id;
        $this->setSlug($id);

        $this->page_title = $page_title;
        $this->menu_title = $menu_title;
        
        return $this;
    }

    public static function create($id, $page_title, $menu_title)
    {
        return new self($id, $page_title, $menu_title);
    }

    public function setSlug($value)
    {
        $this->slug = $value;
        
        return $this;
    }

    public function setIcon($value)
    {
        $this->icon = $value;
        
        return $this;
    }

    public function settingsPageContent()
    {
        $fields = [];

        foreach($this->items as $item) {

            if($item['type'] == 'field') {
                foreach($item['fields'] as $field) {
                    $fields[] = $field->build();
                }
            }
        }

        echo Template::build('SettingsPage/page.html', [
            'id' => uniqid("{$this->id}-", true),
            'page_title' => $this->page_title,
            'settings_fields' => $this->settingsFields(),
            'do_settings_sections' => $this->doSettingsSections("{$this->id}-settings-group"),
            'submit_button' => get_submit_button(),
            'fields' => $fields
        ]);
    }

    private function settingsFields() {
        $output = '<input type="hidden" name="option_page" value="' . esc_attr("{$this->id}-settings-group") . '" />';
        $output .= '<input type="hidden" name="action" value="update" />';
        $output .= wp_nonce_field("{$this->id}-settings-group-options", '_wpnonce', true, false);

        return $output;
    }

    private function doSettingsSections($page) {
        global $wp_settings_sections, $wp_settings_fields;
     
        if(!isset($wp_settings_sections[$page])) {
            return;
        }

        $output = '';
     
        foreach((array) $wp_settings_sections[$page] as $section) {
            if($section['title']) {
                $output .= "<h2>{$section['title']}</h2>\n";
            }
     
            if($section['callback']) {
                call_user_func( $section['callback'], $section );
            }
     
            if(!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']])) {
                continue;
            }
            $output .= '<table class="form-table" role="presentation">';
            $this->doSettingsFields($page, $section['id']);
            $output .= '</table>';
        }

        return $output;
    }

    private function doSettingsFields($page, $section) {
        global $wp_settings_fields;
     
        if (!isset($wp_settings_fields[$page][$section])) {
            return;
        }

        $output = '';
     
        foreach((array) $wp_settings_fields[$page][$section] as $field) {
            $class = '';
     
            if(!empty($field['args']['class'])) {
                $class = ' class="' . esc_attr($field['args']['class']) . '"';
            }
     
            $output .= "<tr{$class}>";
     
            if (!empty($field['args']['label_for'])) {
                $output .= '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
            } else {
                $output .= '<th scope="row">' . $field['title'] . '</th>';
            }
     
            $output .= '<td>';
            call_user_func($field['callback'], $field['args']);
            $output .= '</td>';
            $output .= '</tr>';
        }

        return $output;
    }

    public function addItem($setting_fields = [])
    {
        if(!is_array($setting_fields)) {
            $setting_fields = [$setting_fields];
        }

        $setting_fields = array_filter($setting_fields, function($item) {
            return $item instanceof SettingField;
        });
        
        $this->items[] = [
            'type' => 'field',
            'fields' => $setting_fields
        ];

        return $this;
    }

    public function build()
    {
        add_action('admin_menu', function() {
            add_menu_page($this->page_title, $this->menu_title, $this->capability, $this->slug, [$this, 'settingsPageContent'], $this->icon);
        });

        add_action( 'admin_init', function() {
            foreach($this->items as $item) {
                foreach($item['fields'] as $field) {
                    register_setting("{$this->id}-settings-group", $field->getId());
                }
            }
        });
    }
}