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

use PHPDraft\Out\BaseTemplateRenderer;
use stdClass;

abstract class BaseHtmlGenerator
{
    /**
     * Type of sorting to do.
     *
     * @var int
     */
    public int $sorting;

    /**
     * JSON representation of an API Blueprint.
     *
     * @var object
     */
    protected object $object;

    /**
     * Rendered HTML
     *
     * @var string
     */
    protected string $html;

    /**
     * Constructor.
     *
     * @param object $json Representation of an API Blueprint
     *
     * @return self
     */
    public function init(object $json): self
    {
        $this->object = $json;

        return $this;
    }

    /**
     * Build the HTML representation of the object.
     *
     * @param string      $template Type of template to display.
     * @param string|null $image    Image to use as a logo
     * @param string|null $css      CSS to load
     * @param string|null $js       JS to load
     *
     * @return void
     *
     * @throws ExecutionException As a runtime exception
     */
    abstract public function build_html(string $template = 'default', ?string $image = null, ?string $css = null, ?string $js = null): void;


    /**
     * Get the HTML representation of the object.
     *
     * @return string
     */
    abstract public function __toString();
}
