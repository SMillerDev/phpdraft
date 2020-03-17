<?php
declare(strict_types=1);

/**
 * This file contains the BaseHtmlGenerator.
 *
 * @package PHPDraft\Parse
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse;

use PHPDraft\Out\BaseTemplateGenerator;
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
     * @param object $json JSON representation of an API Blueprint
     */
    public function init(object $json): self
    {
        $this->object = $json;

        return $this;
    }

    abstract public function get_html(string $template = 'default', ?string $image = null, ?string $css = null, ?string $js = null): BaseTemplateGenerator;
}
