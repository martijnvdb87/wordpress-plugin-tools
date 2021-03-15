<?php

namespace Martijnvdb\PayhipProductOverview\Models;

class Action {
    public static function add($hook, $function_to_add, $priority = null, $accepted_args = null)
    {
        add_action($hook, $function_to_add, $priority, $accepted_args);
    }
}