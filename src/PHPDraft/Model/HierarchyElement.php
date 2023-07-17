<?php

declare(strict_types=1);

/**
 * This file contains the APIBlueprintElement class.
 *
 * @package PHPDraft\Model
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

/**
 * Class HierarchyElement.
 */
abstract class HierarchyElement
{
    /**
     * Title of the element.
     *
     * @var string
     */
    public string $title;

    /**
     * Description of the element.
     *
     * @var string
     */
    public string $description;

    /**
     * Child elements.
     *
     * @var HierarchyElement[]
     */
    public array $children = [];

    /**
     * Parse a JSON object to an element.
     *
     * @param object $object an object to parse
     *
     * @return self
     */
    public function parse(object $object): self
    {
        if (isset($object->meta) && isset($object->meta->title)) {
            $this->title = $object->meta->title->content ?? $object->meta->title;
        }

        if (!isset($object->content) || !is_array($object->content)) {
            return $this;
        }

        foreach ($object->content as $key => $item) {
            if ($item->element === 'copy') {
                $this->description = $item->content;
                unset($object->content[$key]);
            }
        }

        if (!empty($object->content)) {
            $object->content = array_slice($object->content, 0);
        }
        return $this;
    }

    /**
     * Get a linkable HREF.
     *
     * @return string
     */
    public function get_href(): string
    {
        $separator = '-';
        $prep      = isset($this->parent) ? $this->parent->get_href() . $separator : '';

        return $prep . str_replace(' ', '-', strtolower($this->title));
    }
}
