<?php
    require_once __DIR__.'/../src/shortcode.php';
    require_once __DIR__.'/../src/dispatcher.php';
    require_once __DIR__.'/loadCodes.php';

    use CMS\ShortCodeDispatcher;

    $shortCodes = [
        new HelloWorld(),
        new Test()
    ];

    $dispatcher = new ShortCodeDispatcher();
    $dispatcher->RegisterShortcodes($shortCodes);

    $test = "Test string that shows [hello_world attribute='can use this' test='nope'][test][/hello_world] through a shortcode.";
    echo $dispatcher->Dispatch($test);