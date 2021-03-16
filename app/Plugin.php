<?php
namespace Martijnvdb\PayhipProductOverview;

use \Martijnvdb\PayhipProductOverview\Models\Action;
use \Martijnvdb\PayhipProductOverview\Models\Posttype;

class Plugin {
    private $settings = [];

    public function __construct($settings = [])
    {
        $this->settings = $settings;
    }

    public function getSetting($key)
    {
        return isset($this->settings[$key]) ? $this->settings[$key] : null;
    }

    public function run()
    {
        $payhip_products = Posttype::create('payhip-products')
            ->setSlug('shop')
            ->isPublic()
            ->addSupport(['thumbnail'])
            ->setLabels([
                'singular_name' => 'Product',
                'add_new_item' => 'Add new Product',
                'add_new' => 'New Product',
                'edit_item' => 'Edit Product',
            ])
            ->build();
    }
}