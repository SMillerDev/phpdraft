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
     * Type of sorting to do
     *
     * @var int
     */
    public $sorting;

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
     * @param string      $template Type of template to display.
     * @param string|null $image    Image to use as a logo
     * @param string|null $css      CSS to load
     * @param string|null $js       JS to load
     *
     * @return string HTML template to display
     */
    public function get_html($template = 'default', $image = null, $css = null, $js = null)
    {
        $gen = new TemplateGenerator($template, $image);

        if(!empty($css)){
            $gen->css[]   = explode(',',$css);
        }
        if(!empty($css)){
            $gen->js[]   = explode(',',$js);
        }
        $gen->sorting = $this->sorting;

        return $gen->get($this->object);
    }

}