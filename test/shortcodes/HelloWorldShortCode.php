<?php
use CMS\ShortCode;

class HelloWorld extends ShortCode
{
    public function __construct()
    {
        $this->name = "hello_world";
        $this->attributes = [
            'test' => null,
            'mytest' => null,
        ];
    }

    public function Run()
    {
        $content = $this->DispatchShortcodesWithinContent();

        return "Hello ".$content." World!";
    }
}