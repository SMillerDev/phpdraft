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
        $this->object = json_decode($json);
    }

    /**
     * @param string $template Type of template to display.
     *
     * @return string HTML template to display
     */
    public function get_html($template = 'default')
    {
        $gen = new TemplateGenerator($template);
        return $gen->get($this->object);
    }

    function __toString()
    {
        return $this->get_html();
    }

}