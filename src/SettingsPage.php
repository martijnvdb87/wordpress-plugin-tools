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

// Prevent direct access
if(!defined('ABSPATH')) {
    exit;
}

/**
 * SettingsPage provides methods for working with settings in Wordpress.
 */
class SettingsPage {

    /**
     * The SettingsPage id.
     * @var string
     */
    private $id;

    /**
     * The SettingsPage page title.
     * @var string
     */
    private $page_title;

    /**
     * The SettingsPage menu title.
     * @var string
     */
    private $menu_title;

    /**
     * The SettingsPage capability.
     * @var string
     */
    private $capability = 'administrator';

    /**
     * The SettingsPage slu.
     * @var string
     */
    private $slug;

    /**
     * The SettingsPage icon.
     * @var string
     */
    private $icon;

    /**
     * An array of CustomFields which should be shown in the SettingsPage.
     * @var array
     */
    private $items = [];

    /**
     * Creates a new instance of the SettingsPage class.
     * 
     * @param  string $id
     */
    public function __construct(string $id)
    {
        $id = sanitize_key($id);
        $this->id = $id;
        $this->setSlug($id);
    }

    /**
     * Create a new instance of the SettingsPage class.
     * 
     * @param  string $id
     * @return SettingsPage
     */
    public static function create(string $id): SettingsPage
    {
        return new self($id);
    }

    /**
     * Converts an ID into a label text.
     * 
     * @param  string $value
     * @return string
     */
    private function convertToLabel(string $value): string
    {
        $value = preg_replace('/[-_]/', ' ', $value);
        $value = ucwords($value);
        return $value;
    }

    /**
     * Set the SettingsPage page title.
     * 
     * @param  string $value
     * @return SettingsPage
     */
    public function setPageTitle(string $value): SettingsPage
    {
        $this->page_title = $value;
        
        if(empty($this->menu_title)) {
            $this->menu_title = $this->convertToLabel($value);
        }
        
        return $this;
    }

    /**
     * Set the SettingsPage menu title.
     * 
     * @param  string $value
     * @return SettingsPage
     */
    public function setMenuTitle(string $value): SettingsPage
    {
        $this->menu_title = $value;

        if(empty($this->page_title)) {
            $this->page_title = $this->convertToLabel($value);
        }
        
        return $this;
    }

    /**
     * Set the SettingsPage slug.
     * 
     * @param  string $value
     * @return SettingsPage
     */
    public function setSlug(string $value): SettingsPage
    {
        $this->slug = $value;
        
        return $this;
    }

    /**
     * Set the SettingsPage icon.
     * 
     * @param  string $value
     * @return SettingsPage
     */
    public function setIcon(string $value): SettingsPage
    {
        $this->icon = $value;
        
        return $this;
    }

    /**
     * The SettingsPage content callback function.
     * 
     * @return void
     */
    public function settingsPageContent(): void
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

    /**
     * Get the default settings fields.
     * 
     * @return string
     */
    private function settingsFields(): string
    {
        $output = '<input type="hidden" name="option_page" value="' . esc_attr("{$this->id}-settings-group") . '" />';
        $output .= '<input type="hidden" name="action" value="update" />';
        $output .= wp_nonce_field("{$this->id}-settings-group-options", '_wpnonce', true, false);

        return $output;
    }

    /**
     * Get the default settings sections.
     * 
     * @return string
     */
    private function doSettingsSections(string $page): string
    {
        global $wp_settings_sections, $wp_settings_fields;
     
        if(!isset($wp_settings_sections[$page])) {
            return '';
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

    /**
     * Get the default settings fields.
     * 
     * @return string
     */
    private function doSettingsFields(string $page, string $section): string
    {
        global $wp_settings_fields;
     
        if (!isset($wp_settings_fields[$page][$section])) {
            return '';
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

    /**
     * Add one or more CustomField to a SettingsPage.
     * 
     * @param  CustomField $setting_field
     * @return SettingsPage
     */
    public function addItem(CustomField $setting_field): SettingsPage
    {
        $setting_field->setPageType('setting');
        
        $this->items[] = [
            'type' => 'field',
            'fields' => $setting_field
        ];

        return $this;
    }

    /**
     * Add one or more CustomField to a SettingsPage.
     * 
     * @param  array $setting_fields
     * @return SettingsPage
     */
    public function addItems(array $setting_fields = []): SettingsPage
    {
        if(!is_array($setting_fields)) {
            $setting_fields = [$setting_fields];
        }

        $setting_fields = array_filter($setting_fields, function($item) {
            return $item instanceof CustomField;
        });

        foreach($setting_fields as $setting_field) {
            $this->addItem($setting_field);
        }

        return $this;
    }

    /**
     * Build the SettingsPage.
     * 
     * @return void
     */
    public function build(): void
    {
        if(empty($this->page_title)) {
            $this->page_title = $this->convertToLabel($this->id);
        }
        
        if(empty($this->menu_title)) {
            $this->menu_title = $this->convertToLabel($this->id);
        }

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