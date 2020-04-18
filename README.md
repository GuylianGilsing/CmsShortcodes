# CmsShortcodes
A (custom written) class based implementation of Wordpress shortcodes.

# Usage
The main components of the implementation can be found within the *src* folder. This folder *should* contain the following files:

- dispatcher.php
- shortcode.php

### dispatcher.php
**Intro**<br/>
This file contains the shortcode dispatcher. It is used to register and dispatch (or run) shortcode classes.

**Usage**
```php
<?php
// Load your shortcode classes in a way that allows you to instantiate the classes.

// Shortcodes always need to be loaded from arrays.
$shortCodes = [
    new HelloWorld(),
    new Test()
];

// Instantiate the shortcode dispatcher and register the array with shortcode classes.
$dispatcher = new ShortCodeDispatcher();
$dispatcher->RegisterShortcodes($shortCodes);

// Test string. This can hold any data you'd like.
$test = "Test string that shows [hello_world attribute='can use this' test='nope'][test][/hello_world] through a shortcode.";

// Dispatch any shortcodes that are registered with the dispatcher.
echo $dispatcher->Dispatch($test);
```

### shortcode.php
**Intro**<br/>
This file contains the base structure of a shortcode. It is ment to be a template that can be extended in your own shortcode class.

**Usage**<br/>
The following shortcode displays "Hello {CONTENT_HERE} World!" when dispatched.
```php
<?php
// Load the shortcode base class in a way that allows you to instantiate it.

class HelloWorld extends ShortCode
{
    public function __construct()
    {
        // Set the name of the shortcode.
        // This name will be used in the tag: [hello_world]
        $this->name = "hello_world";

        // Set the accepted attributes with default values.
        $this->attributes = [
            'test' => null,
            'mytest' => null,
        ];
    }

    public function Run()
    {
        // Dispatches any potential registered shortcodes within the content.
        $content = $this->DispatchShortcodesWithinContent();

        // Return the output of the shortcode.
        return "Hello ".$content." World!";
    }
}
```

This shortcode can be seen in action with the following test string:
```
[hello_world]YOUR CONTENT HERE[/hello_world]
```

**Class implementation usage**<br/>
The shortcode class provides you with a basic setup. Within this setup you need to provide some data through your own shortcode class constructor.

**name**<br/>
The name of the shortcode needs to be set at all times. The name enables your tag to actually be found when you register it with the dispatcher.
```php
class HelloWorld extends ShortCode
{
    public function __construct()
    {
        // Set the name of the shortcode.
        // This name will be used in the tag: [hello_world]
        $this->name = "hello_world";
    }
}
```

**attributes**<br/>
Attributes need to be manually set within your shortcode class constructor. This needs to be an associative array (array with key and value pairs e.g.: "['key' => 'value']"). The key dictates the name of the attribute. The attribute only accepts words, numbers, - and _ characters. Any attempt at using other characters will make your attribute name not work.

After the name is specified, you need to specify a default value. The values will automatically be set whem the shortcode get's dispatched.
```php
class HelloWorld extends ShortCode
{
    public function __construct()
    {
        // Register 'test' and 'othertest' as valid attributes.
        $this->attributes = [
            'test' => null,
            'mytest' => null,
        ];
    }

    public function Run()
    {
        // Access attributes.
        $testAttr = $this->attributes['test'];

        // Access content.
        $content = $this->content;

        // Access the raw shortcode tag.
        $tag = $this->tag;

        // Parse any registered shortcodes within the content.
        $content = $this->DispatchShortcodesWithinContent();
    }
}
```