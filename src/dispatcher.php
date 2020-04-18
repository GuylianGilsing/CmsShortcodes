<?php
namespace CMS;

class ShortCodeDispatcher
{
    private $shortcodeClasses = [];
    
    /**
     * Registers shortcode classes to the dispatcher. This is needed to actually dispatch any shortcodes.
     * 
     * @param array $classes an array of shortcode classes.
     */
    public function RegisterShortcodes(array $classes)
    {
        if(count($classes) > 0)
        {
            foreach($classes as $class)
            {
                if(!isset($this->shortcodeClasses[$class->name]))
                    $this->shortcodeClasses[$class->name] = $class;
            }
        }
    }
    
    /**
     * Dispatches any shortcode in a given string.
     * 
     * @param string $dispatchString The string in which the shortcode(s) are housed.
     * @return string Returns the string with dispatched shortcode content.
     */
    public function Dispatch(string $dispatchString)
    {
        $output = "";
        // Find the shortcode tags

        // Finds any matching shortcodes in the following patterns:
        // [test]
        // [test][/test]
        // [test]with content[/test]
        // [test with attributes]and content[/test]
        preg_match_all('~\[([^\\\/\[\]\s]+)[^\[\]]*\](.*(\[\/\1\]))?~', $dispatchString, $findTagMatches);

        if(isset($findTagMatches) && count($findTagMatches) > 0)
        {
            // Execute all of the shortcodes.
            foreach($findTagMatches[0] as $tag)
            {
                // Extract the name from the shortcode.
                preg_match('~^\[([^\\\/\[\]\s]+)~', $tag, $shortcodeName);
                $shortcodeName = preg_replace('~[\[\]]~', '', $shortcodeName[0]);

                // Make sure that the shortcode is registered within the dispatcher.
                if(isset($this->shortcodeClasses[$shortcodeName]))
                {
                    // Shortcode matches.

                    // Filter out the shortcode attributes.
                    // These attributes will be send into the actual shortcode class.
                    // preg_match_all('~[\w\d-_]+=["\'][^"\'\[\]]+["\']~', $tag, $shortcodeAttributes);
                    preg_match_all('~(\w+?)=[\'"](.+?)[\'"]~', $tag, $shortcodeAttributes);
                    
                    // Parse the attributes to an associative array.
                    $parsedAttributes = [];
                    foreach($shortcodeAttributes[0] as $attr)
                    {
                        // Get the part before the '=' sign.
                        preg_match('~[\w\d-_]+(?=\=)~', $attr, $name);

                        // Get the part inside the " or '
                        preg_match('~["\'](.*)["\']~', $attr, $content);

                        // Regex matches the ' or " that the content is wrapped around in.
                        // Use substr to remove those commas.
                        $parsedAttributes[$name[0]] = substr($content[0], 1, -1);
                    }
                    $shortcodeAttributes = $parsedAttributes;
                    
                    // Get the content of the shortcode.
                    preg_match('~(?<=\])(.*?)(?=\[\/)~', $tag, $shortcodeContent);

                    if(count($shortcodeContent) > 0)
                        $shortcodeContent = $shortcodeContent[0];
                    else
                        $shortcodeContent = "";

                    // Insert the data into the shortcode and execute it.
                    $executedShortcode = $this->shortcodeClasses[$shortcodeName];
                    $executedShortcode->Attributes($shortcodeAttributes);
                    $executedShortcode->dispatcher = $this;
                    $executedShortcode->content = $shortcodeContent;
                    $executedShortcode->tag = $tag;

                    $this->shortcodeClasses[$shortcodeName] = $executedShortcode->Run();

                    // Replace every occurence of the shortcode in the string.
                    $output = str_replace($tag, $this->shortcodeClasses[$shortcodeName], $dispatchString);
                }
            }
        }

        return $output;
    }
}