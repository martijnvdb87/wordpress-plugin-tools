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
$custom_metabox = MetaBox::create('custom-metabox')
    ->addPostType('page')
    ->addItem(
        CustomField::create('custom-field')
            ->setType('textarea')
            ->setLabel('page-custom-textarea')
    )
    ->build();
```


### PostType
Create a new PostType:
```php
$custom_posttype = new PostType('custom-posttype');

// Or use the static create method
$another_posttype = PostType::create('another-posttype');
```

##### Add a MetaBox
```php
$custom_posttype->addMetaBox(MetaBox::create('custom-metabox'));

// Or add multiple at once
$custom_posttype->addMetaBox([
    MetaBox::create('custom-metabox'),
    MetaBox::create('another-metabox'),
]);
```

### CustomField
```php
$customfield = CustomField::create('my-customfield');
$customfield->build();
```

### MetaBox
```php
$metabox = MetaBox::create('my-metabox');
$metabox->build();
```

### SettingsPage
```php
$settingspage = SettingsPage::create('my-settingspage');
$settingspage->build();
```