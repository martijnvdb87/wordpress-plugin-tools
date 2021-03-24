<?php
/**
 * Plugin Name:       Payhip Product Overview
 * Plugin URI:        https://github.com/martijnvdb87/wp-plugin-payhip-product-overview
 * Description:       Generates an overview page of your Payhip products.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.0
 * Author:            Martijn van den Bosch
 * Author URI:        https://martijnvandenbosch.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       payhip-product-overview
 * Domain Path:       /languages
 */

namespace Martijnvdb\PayhipProductOverview;

use Martijnvdb\PayhipProductOverview\Models\{PostType, CustomField, MetaBox, Translation, SettingsPage, SettingField};

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

$metabox_testing = MetaBox::create('just-testing')
    ->addItem([$customfield_testing_1])
    ->addItem([$customfield_testing_4])
    ->addList(Translation::get('List label'), [$customfield_testing_2, $customfield_testing_3, $customfield_testing_5])
    ->build();

$settingfield_1 = SettingField::create('just-testing', 'text')->setLabel(Translation::get('Custom field 1'));

$settingspage_1 = SettingsPage::create('yay', 'yay', 'yay')
    ->addItem([$settingfield_1])
    ->build();