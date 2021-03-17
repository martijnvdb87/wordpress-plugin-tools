<?php

namespace Martijnvdb\PayhipProductOverview\Models;

class CustomField {

    private $id;
    private $title;
    private $type;

    public function __construct($id)
    {
        $id = sanitize_key($id);
        $this->id = $id;
        
        return $this;
    }

    public static function create($id)
    {
        return new self($id);
    }

    public function build()
    {
        //
    }
}