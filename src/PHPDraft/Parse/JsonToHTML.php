<?php
/**
 * This file contains the JsonToHTML.
 *
 * @package PHPDraft\Parse
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse;

use PHPDraft\Out\TemplateGenerator;
use stdClass;

/**
 * Class JsonToHTML.
 */
class JsonToHTML
{
    /**
     * Type of sorting to do.
     *
     * @var int
     */
    public $sorting;

    /**
     * JSON representation of an API Blueprint.
     *
     * @var stdClass
     */
    protected $object;

    /**
     * JsonToHTML constructor.
     *
     * @param stdClass $json JSON representation of an API Blueprint
     */
    public function __construct(stdClass $json)
    {
        $this->object = $json;
    }

    /**
     * Gets the default template HTML.
     *
     * @throws ExecutionException When parsing fails
     *
     * @return string
     */
    public function __toString()
    {
        return $this->get_html();
    }

    /**
     * Get the HTML representation of the JSON object.
     *
     * @param string      $template Type of template to display.
     * @param string|null $image    Image to use as a logo
     * @param string|null $css      CSS to load
     * @param string|null $js       JS to load
     *
     * @throws ExecutionException As a runtime exception
     *
     * @return TemplateGenerator HTML template to display
     */
    public function get_html(string $template = 'default', ?string $image = NULL, ?string $css = NULL, ?string $js = NULL): TemplateGenerator
    {
        $gen = new TemplateGenerator($template, $image);

        if (!empty($css)) {
            $gen->css[] = explode(',', $css);
        }

        if (!empty($js)) {
            $gen->js[] = explode(',', $js);
        }

        $gen->sorting = $this->sorting;

        $gen->get($this->object);

        return $gen;
    }
}
