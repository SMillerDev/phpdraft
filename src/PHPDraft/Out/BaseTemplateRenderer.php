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
use PHPDraft\Model\Elements\BasicStructureElement;
use PHPDraft\Model\Elements\ObjectStructureElement;

abstract class BaseTemplateRenderer
{
    /**
     * Type of sorting to do on objects.
     *
     * @var Sorting
     */
    public Sorting $sorting;
    /**
     * CSS Files to load.
     *
     * @var string[]
     */
    public array $css = [];
    /**
     * JS Files to load.
     *
     * @var string[]
     */
    public array $js = [];
    /**
     * JSON object of the API blueprint.
     *
     * @var array<object>
     */
    protected array $categories = [];
    /**
     * The template file to load.
     *
     * @var string
     */
    protected string $template;
    /**
     * The image to use as a logo.
     *
     * @var string|null
     */
    protected ?string $image = null;
    /**
     * The base data of the API.
     *
     * @var array<string, mixed>
     */
    protected array $base_data;
    /**
     * Structures used in all data.
     *
     * @var BasicStructureElement[]
     */
    protected array $base_structures = [];
}
