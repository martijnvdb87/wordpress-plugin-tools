<?php
namespace Martijnvdb\PayhipProductOverview;

use \Martijnvdb\PayhipProductOverview\Models\Action;

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
        //
    }
}