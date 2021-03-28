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

Every chain should end with a `build()` method. The `build()` method will register the object using the Wordpress action hooks. Except when using an object as an argument in a method. In the following example, the CustomField object should not end with the `build()` method:
```php
$custom_field = CustomField::create('custom-field')
    ->setType('textarea')
    ->setLabel('page-custom-textarea');

$custom_metabox = MetaBox::create('custom-metabox')
    ->addPostType('page')
    ->addCustomField($custom_field)
    ->build();
```

## PostType
This object allows you to easily create one or multiple post types without having to worry about the Wordpress hooks. Chain any method you like and end with the `build()` method to register the post type.

#### Create a new PostType
```php
$custom_posttype = PostType::create('custom-posttype')->build();
```

#### Add a MetaBox to the PostType
MetaBoxes on their own won't be shown anywhere. They have to be added to a PostType. These methods will do exactly that.
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
All the Wordpress labels can be used. [See a full list of supported labels](https://developer.wordpress.org/reference/functions/get_post_type_labels/).
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


#### Add a description to the PostType
```php
$custom_posttype = PostType::create('custom-posttype')
    ->setDescription('A very interesting description')
    ->build();
```

#### Make the PostType public
PostTypes are `false` by default. Using this method the PostType will be shown in the admin interface.
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

#### Set the icon of the PostType
```php
$custom_posttype = PostType::create('custom-posttype')
    ->setIcon('dashicons-thumbs-up')
    ->build();
```

#### Add feature support to the PostType
Any Wordpress core feature can be used. The core features are `title`, `editor`, `comments`, `revisions`, `trackbacks`, `author`, `excerpt`, `page-attributes`, `thumbnail`, `custom-fields` and `post-formats`.
```php
$custom_posttype = PostType::create('custom-posttype')
    ->addSupport(['title', 'thumbnail', 'comments']) // Must be an array
    ->build();
```

[See a full list of supported features](https://developer.wordpress.org/reference/functions/add_post_type_support/)

#### Remove feature support from the PostType
Any Wordpress core feature can be removed. The core features are `title`, `editor`, `comments`, `revisions`, `trackbacks`, `author`, `excerpt`, `page-attributes`, `thumbnail`, `custom-fields` and `post-formats`.
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
This library only has a handfull of dedicated methods to set post type options. To use any other post type option you can use the `addOption()` method. [See a full list of possible options](https://developer.wordpress.org/reference/functions/register_post_type/).
```php
$custom_posttype = PostType::create('custom-posttype')
    // Some examples
    ->addOption('show_in_admin_bar', false)
    ->addOption('show_in_nav_menus', false)
    ->addOption('has_archive', true)
    ->build();
```

## CustomField
This object allows you to easily create one or multiple custom fields without having to worry about the Wordpress hooks. Chain any method you like and end with the `build()` method to register the custom field.

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
    ->addOption('first-option', 'This is the first option')
    ->addOption('second-option', 'This is the second option')

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

## MetaBox
This object allows you to easily create one or multiple metaboxes without having to worry about the Wordpress hooks. Chain any method you like and end with the `build()` method to register the metabox.

#### Create a new MetaBox
```php
$custom_metabox = MetaBox::create('custom-metabox')->build();
```

#### Set the title of the MetaBox
```php
$custom_metabox = MetaBox::create('custom-metabox')
    ->setTitle('Metabox title')
    ->build();
```

#### Add a CustomField to a MetaBox
```php
$first_customfield = CustomField::create('first-customfield');
$second_customfield = CustomField::create('second-customfield');
$third_customfield = CustomField::create('third-customfield');

$custom_metabox = MetaBox::create('custom-metabox')
    ->addCustomField($first_customfield)

    // Or add multiple at once
    ->addCustomFields([
        $second_customfield,
        $third_customfield,
    ])
    ->build();
```

#### Add a list to a MetaBox
This library allows you to easily create a growable and reorderable list of items. Each item in the list can contain multiple CustomFields. If for example you would like to add multiple URLs with a title and a description to a post, you can use a list for this.
```php
$first_customfield = CustomField::create('first-customfield');
$second_customfield = CustomField::create('second-customfield');
$third_customfield = CustomField::create('third-customfield');

$custom_metabox = MetaBox::create('custom-metabox')
    ->addList('custom-list', [
        $first_customfield,
        $second_customfield,
        $third_customfield
    ])
    ->build();
```

#### Add the MetaBox to a Wordpress post type
The ID of the post types has to be used as an argument of these methods. These methods allow you to add MetaBoxes to existing post types which aren't created with this library.
```php
$custom_metabox = MetaBox::create('custom-metabox')
    ->addPostType('page')

    // Or add multiple at once
    ->addPostTypes([
        'custom-posttype',
        'another-posttype',
    ])
    ->build();
```

#### Customize text
This library uses two text strings in the MetaBox which can be customized or translated. The following texts are used:
- `new` New
- `delete_confirm` Are you sure you want to delete this item?

This is how to customize them:
```php
$custom_metabox = MetaBox::create('custom-metabox')
    ->setText('new', 'Nieuwe lijst')

    // Or customize multiple at once
    ->setTexts([
        'new' => 'Nieuwe lijst',
        'delete_confirm' => 'Weet u zeker dat u deze lijst wil verwijderen?'
    ])
    ->build();
```

## SettingsPage
This object allows you to easily create one or multiple setting pages without having to worry about the Wordpress hooks. Chain any method you like and end with the `build()` method to register the setting page.

```php
$custom_settingspage = SettingsPage::create('custom-settingspage')->build();
```

#### Set the page title of the SettingsPage
```php
$custom_settingspage = SettingsPage::create('custom-settingspage')
    ->setPageTitle('The page title')
    ->build();
```

#### Set the menu title of the SettingsPage
```php
$custom_settingspage = SettingsPage::create('custom-settingspage')
    ->setMenuTitle('Menu title')
    ->build();
```

#### Set the slug of the SettingsPage
```php
$custom_settingspage = SettingsPage::create('custom-settingspage')
    ->setSlug('settingspage-slug')
    ->build();
```

#### Set the icon of the SettingsPage
```php
$custom_settingspage = SettingsPage::create('custom-settingspage')
    ->setIcon('dashicons-thumbs-up')
    ->build();
```

#### Add CustomFields to the SettingsPage
```php
$first_customfield = CustomField::create('first-customfield');
$second_customfield = CustomField::create('second-customfield');
$third_customfield = CustomField::create('third-customfield');

$custom_settingspage = SettingsPage::create('custom-settingspage')
    ->addCustomField($first_customfield)

    // Or add multiple at once
    ->addCustomFields([
        $second_customfield,
        $third_customfield,
    ])
    ->build();
```
