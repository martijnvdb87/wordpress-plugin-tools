# Wordpress Plugin Tools
The goal of this library is to simplify the proces of creating custom post types and settings pages in Wordpress.

## Installation
You can install the package via composer:
```bash
composer require martijnvdb/wordpress-plugins-tools
```

## Usage
All documented objects use a fluent interface, which allows you to chain methods. For example:
```php
$custom_posttype = PostType::create('custom-posttype')
    ->setDescription('A very interesting description')
    ->setSlug('custom-slug')
    ->setIcon('dashicons-thumbs-up')
    ->setPublic()
    ->build();
```

Every chain should end with a `build()` method. Except when using an object as an argument in a method. In this example, the CustomField object should not end with the `build()` method:
```php
$custom_field = CustomField::create('custom-field')
    ->setType('textarea')
    ->setLabel('page-custom-textarea');

$custom_metabox = MetaBox::create('custom-metabox')
    ->addPostType('page')
    ->addItem($custom_field)
    ->build();
```

### PostType

#### Create a new PostType
```php
$custom_posttype = PostType::create('custom-posttype')->build();
```

#### Add a MetaBox to the PostType
```php
$first_metabox = MetaBox::create('first-metabox');
$second_metabox = MetaBox::create('second-metabox');
$third_metabox = MetaBox::create('third-metabox');

$custom_posttype = PostType::create('custom-posttype')
    ->addMetaBox($first_metabox) // Add a single MetaBox
    ->addMetaBoxes([$second_metabox, $third_metabox])// Or add multiple at once
    ->build();
```

#### Add labels to the PostType
```php
$products_posttype = PostType::create('products')
    ->setLabel('name', 'Products') // Add a single label

    // Or add multiple at once
    ->setLabels([
        'singular_name' => 'Product',
        'add_new_item' => 'Add new Product',
        'add_new' => 'New Product',
        'edit_item' => 'Edit Product',
    ])
    ->build();
```

[See a full list of supported labels](https://developer.wordpress.org/reference/functions/get_post_type_labels/)

#### Add a description to the PostType
```php
$custom_posttype = PostType::create('custom-posttype')
    ->setDescription('A very interesting description')
    ->build();
```

#### Make the PostType public
```php
$custom_posttype = PostType::create('custom-posttype')
    ->setPublic()
    ->build();
```

#### Set menu position of the PostType
```php
$custom_posttype = PostType::create('custom-posttype')
    ->setMenuPosition(8)
    ->build();
```

#### Set menu position of the PostType
```php
$custom_posttype = PostType::create('custom-posttype')
    ->setIcon('dashicons-thumbs-up')
    ->build();
```

#### Add feature support to the PostType
```php
$custom_posttype = PostType::create('custom-posttype')
    ->addSupport(['title', 'thumbnail', 'comments']) // Must be an array
    ->build();
```

[See a full list of supported features](https://developer.wordpress.org/reference/functions/add_post_type_support/)

#### Remove feature support from the PostType
```php
$custom_posttype = PostType::create('custom-posttype')
    ->removeSupport(['editor']) // Must be an array
    ->build();
```

[See a full list of supported features](https://developer.wordpress.org/reference/functions/add_post_type_support/)

#### Set the slug of the PostType
```php
$custom_posttype = PostType::create('custom-posttype')
    ->setSlug('custom-slug')
    ->build();
```

#### Use the block editor in the PostType
```php
$custom_posttype = PostType::create('custom-posttype')
    ->addBlockEditor()
    ->build();
```

#### Add any supported option to the PostType
```php
$custom_posttype = PostType::create('custom-posttype')
    // Some examples
    ->addOption('show_in_admin_bar', false)
    ->addOption('show_in_nav_menus', false)
    ->addOption('has_archive', true)
    ->build();
```

[See a full list of possible options](https://developer.wordpress.org/reference/functions/register_post_type/)

### CustomField

#### Create a new CustomField
```php
$customfield = CustomField::create('new-customfield')->build();
```

#### Set the CustomField type
The possible CustomField types are `text`, `textarea`, `checkbox`, `number`, `select`, `radio` and `editor`.
```php
$new_customfield = CustomField::create('new-customfield')
    ->setType('textarea')
    ->build();
```

#### Set the label of the CustomField
```php
$new_customfield = CustomField::create('new-customfield')
    ->setLabel('New custom field')
    ->build();
```

#### Add options to the CustomField
This will only be used if the CustomField is a `select` or `radio` type.
```php
$new_customfield = CustomField::create('new-customfield')
    ->setType('select') // Or 'radio'
    ->addOption('first-option', 'This is the first option');
    ->addOption('second-option', 'This is the second option');

    // Or add multiple at once
    ->addOptions([
        'third-option' => 'This is the third option',
        'fourth-option' => 'This is the fourth option'
    ]);
    ->build();
```

#### Set the minimal value of the CustomField
This will only be used if the CustomField is a `number` type.
```php
$new_customfield = CustomField::create('new-customfield')
    ->setType('number')
    ->setMin(0)
    ->build();
```

#### Set the maximum value of the CustomField
This will only be used if the CustomField is a `number` type.
```php
$new_customfield = CustomField::create('new-customfield')
    ->setType('number')
    ->setMax(100)
    ->build();
```

#### Set the size of the steps of the CustomField
This will only be used if the CustomField is a `number` type.
```php
$new_customfield = CustomField::create('new-customfield')
    ->setType('number')
    ->setStep(10)
    ->build();
```

### MetaBox
```php
$metabox = MetaBox::create('my-metabox')->build();
```

### SettingsPage
```php
$settingspage = SettingsPage::create('my-settingspage')->build();
```