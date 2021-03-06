<?php

declare(strict_types=1);

/**
 * This file contains the BaseTemplateGenerator.php.
 *
 * @package PHPDraft\Out
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out;

use Lukasoppermann\Httpstatus\Httpstatus;
use PHPDraft\Model\Elements\ObjectStructureElement;

abstract class BaseTemplateRenderer
{
    /**
     * Type of sorting to do on objects.
     *
     * @var int
     */
    public $sorting;
    /**
     * CSS Files to load.
     *
     * @var string[]
     */
    public $css = [];
    /**
     * JS Files to load.
     *
     * @var string[]
     */
    public $js = [];
    /**
     * JSON object of the API blueprint.
     *
     * @var mixed
     */
    protected $categories = [];
    /**
     * The template file to load.
     *
     * @var string
     */
    protected $template;
    /**
     * The image to use as a logo.
     *
     * @var string|null
     */
    protected $image = null;
    /**
     * The base data of the API.
     *
     * @var array<mixed>
     */
    protected $base_data;
    /**
     * Structures used in all data.
     *
     * @var ObjectStructureElement[]
     */
    protected $base_structures = [];
}
