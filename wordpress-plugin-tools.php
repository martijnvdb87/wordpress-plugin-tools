<?php
/**
 * Plugin Name:       Wordpress Plugin Tools
 * Plugin URI:        https://github.com/martijnvdb87/wordpress-plugin-tools
 * Description:       A simple library to quickly create Wordpress plugins.
 * Version:           0.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.0
 * Author:            Martijn van den Bosch
 * Author URI:        https://martijnvandenbosch.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wordpress-plugin-tools
 * Domain Path:       /languages
 */

namespace Martijnvdb\WordpressPluginTools;

use Martijnvdb\WordpressPluginTools\{PostType, CustomField, MetaBox, Translation, SettingsPage, SettingField};

require_once __DIR__ . '/vendor/autoload.php';

$payhip_products_posttype = PostType::create('payhip-products')
    ->setSlug('shop')
    ->setPublic()
    ->addSupport(['thumbnail'])
    ->setLabels([
        'singular_name' => 'Product',
        'add_new_item' => 'Add new Product',
        'add_new' => 'New Product',
        'edit_item' => 'Edit Product',
    ])
    //->addBlockEditor()
    ->build();

$customfield_testing_1 = CustomField::create('just-testing', 'text')->setLabel(Translation::get('Custom field 1'));
$customfield_testing_2 = CustomField::create('just-testing1', 'text')->setLabel(Translation::get('Custom field 2'));
$customfield_testing_3 = CustomField::create('just-testing2', 'textarea')->setLabel(Translation::get('Custom field 3'));
$customfield_testing_4 = CustomField::create('just-testing3', 'number')->setLabel(Translation::get('Custom field 4'));
$customfield_testing_5 = CustomField::create('just-testing4', 'checkbox')->setLabel(Translation::get('Custom field 5'));
$customfield_testing_6 = CustomField::create('just-testing5', 'radio')->setLabel(Translation::get('Custom field 6'))->addOptions([
    'yay' => 'Yay',
    'sdfsf' => 'sdfsdf',
    ]);
$customfield_testing_7 = CustomField::create('just-testing6', 'editor')->setLabel(Translation::get('Custom field 7'));

$metabox_testing = MetaBox::create('just-testing')
    ->addItem([$customfield_testing_1])
    ->addItem([$customfield_testing_4, $customfield_testing_7])
    ->addList(Translation::get('List label'), [$customfield_testing_2, $customfield_testing_3, $customfield_testing_5, $customfield_testing_6])
    ->build();

$settingspage_1 = SettingsPage::create('yay', 'yay', 'yay')
    ->addItem([
        SettingField::create('just-testing', 'text')->setLabel(Translation::get('Custom field 1')),
        SettingField::create('just-testing2', 'textarea')->setLabel(Translation::get('Custom field 1')),
        SettingField::create('just-testing3', 'number')->setLabel(Translation::get('Custom field 1')),
        SettingField::create('just-testing4', 'checkbox')->setLabel(Translation::get('Custom field 1')),
        SettingField::create('just-testing5', 'select')->setLabel(Translation::get('Custom field 1'))->addOptions([
            'yay' => 'Yay',
            'sdfsf' => 'sdfsdf',
        ]),
        SettingField::create('just-testing5', 'radio')->setLabel(Translation::get('Custom field 1'))->addOptions([
            'yay' => 'Yay',
            'sdfsf' => 'sdfsdf',
        ]),
        SettingField::create('just-testing6', 'editor')->setLabel(Translation::get('Custom field 1')),
    ])
    ->build();