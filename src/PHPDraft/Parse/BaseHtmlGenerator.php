<?php
/**
 * This file contains the BaseHtmlGenerator.
 *
 * @package PHPDraft\Parse
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse;


use PHPDraft\Out\BaseTemplateGenerator;
use PHPDraft\Out\TemplateGenerator;
use stdClass;

abstract class BaseHtmlGenerator
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
    public function init(stdClass $json): self
    {
        $this->object = $json;
        return $this;
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

    public abstract function get_html(string $template = 'default', ?string $image = NULL, ?string $css = NULL, ?string $js = NULL): BaseTemplateGenerator;
}