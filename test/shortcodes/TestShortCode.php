<?php
use CMS\ShortCode;

class Test extends ShortCode
{
    public function __construct()
    {
        $this->name = "test";
    }

    public function Run()
    {
        return "(with content of a different shortcode)";
    }
}