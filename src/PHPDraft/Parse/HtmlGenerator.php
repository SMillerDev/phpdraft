<?php

declare(strict_types=1);

/**
 * This file contains the HtmlGenerator.
 *
 * @package PHPDraft\Parse
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse;

use PHPDraft\Out\TemplateRenderer;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class HtmlGenerator.
 */
class HtmlGenerator extends BaseHtmlGenerator
{
    /**
     * Get the HTML representation of the JSON object.
     *
     * @param string      $template Type of template to display.
     * @param string|null $image Image to use as a logo
     * @param string|null $css CSS to load
     * @param string|null $js JS to load
     *
     * @return void HTML template to display
     *
     * @throws ExecutionException As a runtime exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function build_html(string $template = 'default', ?string $image = null, ?string $css = null, ?string $js = null): void
    {
        $gen = new TemplateRenderer($template, $image);

        if (!is_null($css)) {
            $gen->css = explode(',', $css);
        }

        if (!is_null($js)) {
            $gen->js = explode(',', $js);
        }

        $gen->sorting = $this->sorting;

        $this->html = $gen->get($this->object);
    }

    /**
     * Returns the generated HTML.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->html;
    }
}
