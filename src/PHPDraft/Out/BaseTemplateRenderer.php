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

use PHPDraft\Model\Category;
use PHPDraft\Model\Elements\BasicStructureElement;

abstract class BaseTemplateRenderer
{
    /**
     * Type of sorting to do on objects.
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
     * The base data of the API.
     *
     * @var array<string, mixed>
     */
    protected array $base_data = [];

    /**
     * JSON object of the API blueprint.
     *
     * @var Category[]
     */
    protected array $categories = [];
    /**
     * Structures used in all data.
     *
     * @var BasicStructureElement[]
     */
    protected array $base_structures = [];

    /**
     * Parse base data
     *
     * @param object $object
     */
    protected function parse_base_data(object $object): void
    {
        //Prepare base data
        if (!is_array($object->content[0]->content)) {
            return;
        }

        $this->base_data['TITLE'] = $object->content[0]->meta->title->content ?? '';

        foreach ($object->content[0]->attributes->metadata->content as $meta) {
            $this->base_data[$meta->content->key->content] = $meta->content->value->content;
        }

        foreach ($object->content[0]->content as $value) {
            if ($value->element === 'copy') {
                $this->base_data['DESC'] = $value->content;
                continue;
            }

            $cat = new Category();
            $cat = $cat->parse($value);

            if (($value->meta->classes->content[0]->content ?? null) === 'dataStructures') {
                $this->base_structures = array_merge($this->base_structures, $cat->structures);
            } else {
                $this->categories[] = $cat;
            }
        }
    }
}
