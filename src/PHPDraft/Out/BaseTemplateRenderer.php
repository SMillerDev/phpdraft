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
use PHPDraft\Model\Category;
use PHPDraft\Model\Elements\ObjectStructureElement;

abstract class BaseTemplateRenderer
{
    /**
     * Type of sorting to do on objects.
     *
     * @var int
     */
    public int $sorting;
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
     * The image to use as a logo.
     *
     * @var string|null
     */
    protected ?string $image = null;
    /**
     * The template file to load.
     *
     * @var string
     */
    protected string $template;
    /**
     * The base data of the API.
     *
     * @var array<int|string, mixed>
     */
    protected array $base_data;
    /**
     * JSON object of the API blueprint.
     *
     * @var Category[]
     */
    protected array $categories = [];
    /**
     * Structures used in all data.
     *
     * @var ObjectStructureElement[]
     */
    protected array $base_structures = [];
}
