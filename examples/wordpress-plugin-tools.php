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
 */

namespace Martijnvdb\WordpressPluginTools;

use Martijnvdb\WordpressPluginTools\{PostType, CustomField, MetaBox, SettingsPage};

require_once __DIR__ . '/vendor/autoload.php';

$product_review = CustomField::create('product-review')->setType('editor');

$score = CustomField::create('score')
    ->setLabel('Overall Score')
    ->setType('number')
    ->setMin(0)
    ->setMax(100)
    ->setStep(10);

$positive_title = CustomField::create('positive-title')
    ->setType('text')
    ->setLabel('Title');

$positive_description = CustomField::create('positive-description')
    ->setType('textarea')
    ->setLabel('Description');

$negative_title = CustomField::create('negative-title')
    ->setType('text')
    ->setLabel('Title');

$negative_description = CustomField::create('negative-description')
    ->setType('textarea')
    ->setLabel('Description');



$custom_metabox = MetaBox::create('custom-metabox')
    ->setTitle('Product review')
    ->addCustomFields([
        $product_review,
        $score,
    ])
    ->addList('Positives', [
        $positive_title,
        $positive_description
    ])
    ->addList('Negatives', [
        $negative_title,
        $negative_description
    ]);



$custom_posttype = PostType::create('product-review')
    ->removeSupport(['editor'])
    ->setIcon('dashicons-thumbs-up')
    ->addMetaBox($custom_metabox)
    ->setPublic()
    ->build();



$show_reviews = CustomField::create('show-reviews')
    ->setLabel('How many reviews should be shown?')
    ->setType('number')
    ->setMin(0)
    ->setMax(100);
    
$contact_email = CustomField::create('contact-email')
    ->setLabel('Contact E-mailadres')
    ->setType('text');

$custom_settingspage = SettingsPage::create('custom-settingspage')
    ->setPageTitle('Reviews Settings')
    ->setMenuTitle('Reviews Settings')
    ->addCustomFields([
        $show_reviews,
        $contact_email
    ])
    ->build();