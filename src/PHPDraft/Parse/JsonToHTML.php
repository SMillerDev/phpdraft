<?php
/**
 * This file contains the JsonToHTML
 *
 * @package PHPDraft\Parse
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse;

use PHPDraft\Out\TemplateGenerator;

class JsonToHTML
{
    /**
     * JSON representation of an API Blueprint
     *
     * @var \stdClass
     */
    protected $object;

    /**
     * JsonToHTML constructor.
     *
     * @param string $json JSON representation of an API Blueprint
     */
    public function __construct($json)
    {
        $this->object = $json;
    }

    /**
     * Gets the default template HTML
     *
     * @return string
     */
    function __toString()
    {
        return $this->get_html();
    }

    /**
     * Get the HTML representation of the JSON object
     *
     * @param string $template Type of template to display.
     *
     * @param string $image    Image to use as a logo
     *
     * @return string HTML template to display
     */
    public function get_html($template = 'default', $image = NULL)
    {
        $gen = new TemplateGenerator($template, $image);

        return $gen->get($this->object);
    }

}