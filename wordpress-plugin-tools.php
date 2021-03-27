<?php
/**
 * Plugin Name:       Wordpress Plugin Tools
 * Plugin URI:        https://github.com/martijnvdb87/wordpress-plugin-tools
 * Description:       A simple library to quickly create Wordpress plugins.
 * Version:           1.0.0
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

use Martijnvdb\WordpressPluginTools\{PostType, CustomField, MetaBox, SettingsPage};

require_once __DIR__ . '/vendor/autoload.php';

// $metabox_testing = MetaBox::create('just-testing')
//     ->addItems([
//         CustomField::create('just-testing')
//             ->setType('text')
//             ->setLabel('Custom field 1'),

//         CustomField::create('just-testing1')
//             ->setType('text')
//             ->setLabel('Custom field 2'),
//     ])

//     ->addList('List label', [
//         CustomField::create('just-testing2')
//             ->setType('textarea')
//             ->setLabel('Custom field 3'),

//         CustomField::create('just-testing3')
//             ->setType('number')
//             ->setLabel('Custom field 4'),

//         CustomField::create('just-testing4')
//             ->setType('checkbox')
//             ->setLabel('Custom field 5'),

//         CustomField::create('just-testing5')
//             ->setType('radio')
//             ->setLabel('Custom field 6')
//             ->addOptions([
//                 'yay' => 'Yay',
//                 'sdfsf' => 'sdfsdf',
//             ]),
//     ]);
    
// $payhip_products_posttype = PostType::create('my-posttype')
//     ->setSlug('my-slug')
//     ->setPublic()
//     ->addSupport(['thumbnail'])
//     ->setLabels([
//         'singular_name' => 'Product',
//         'add_new_item' => 'Add new Product',
//         'add_new' => 'New Product',
//         'edit_item' => 'Edit Product',
//     ])
//     ->addBlockEditor()
//     ->addMetaBox([
//         $metabox_testing
//     ])
//     ->build();

    
// $payhip_products_posttypeq = PostType::create('my-yay')->setPublic()
// ->addMetaBox([
//     $metabox_testing
// ])->build();

// $metabox_testing->build();

// $settingspage_1 = SettingsPage::create('yay')
//     ->setPageTitle('Page Title')
//     ->setMenuTitle('Menu Title')
//     ->addItems([
//         CustomField::create('just-testing')
//             ->setType('text')
//             ->setLabel('Custom field 1'),

//         CustomField::create('just-testing1')
//             ->setType('text')
//             ->setLabel('Custom field 2'),

//         CustomField::create('just-testing2')
//             ->setType('textarea')
//             ->setLabel('Custom field 3'),
            
//         CustomField::create('just-testing3')
//             ->setType('number')
//             ->setLabel('Custom field 4'),
//         CustomField::create('just-testing4')
//             ->setType('checkbox')
//             ->setLabel('Custom field 5'),

//         CustomField::create('just-testing5')
//             ->setType('radio')
//             ->setLabel('Custom field 6')
//             ->addOptions([
//                 'yay' => 'Yay',
//                 'sdfsf' => 'sdfsdf',
//             ]),

//         CustomField::create('just-testing6')
//             ->setType('editor')
//             ->setLabel('Custom field 7')
//     ])
//     ->build();

    $custom_posttype = new PostType('custom-posttype');
    $custom_posttype->setDescription('A very interesting description')
    ->setSlug('custom-slug')
    ->setIcon('dashicons-thumbs-up')
    ->setPublic()
    ->build();

    $custom_metabox = MetaBox::create('custom-metabox')
    ->addPostType('page')
    ->addItem(
        CustomField::create('custom-field')
            ->setType('textarea')
            ->setLabel('page-custom-textarea')
    )
    ->build();