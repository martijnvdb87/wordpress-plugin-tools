<?php

namespace Martijnvdb\PayhipProductOverview\Models;

class MetaBox {

    private $id;
    private $title = '';
    private $posttypes = [];
    private $context = 'normal';
    private $priority = 'default';

    public function __construct($id)
    {
        $id = sanitize_key($id);
        $this->id = $id;
        $this->title = $this->convertToLabel($id);
        
        return $this;
    }

    private function convertToLabel($value)
    {
        $value = preg_replace('/[-_]/', ' ', $value);
        $value = ucwords($value);
        return $value;
    }

    public static function create($id)
    {
        return new self($id);
    }

    public function metaBox()
    {
        add_meta_box($this->id, $this->title, [$this, 'metaBoxContent'], $this->posttypes, $this->context, $this->priority);
    }

	public function metaBoxContent()
    {
        echo "METABOX";
	}

    public function build()
    {
        add_action('add_meta_boxes', [$this, 'metaBox']);
    }
}