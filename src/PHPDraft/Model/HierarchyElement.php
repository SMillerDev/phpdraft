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
     * @var string|null
     */
    public ?string $description = null;

    /**
     * Child elements.
     *
     * @var HierarchyElement[]
     */
    public array $children = [];

    /**
     * Parent Element.
     *
     * @var HierarchyElement|null
     */
    protected ?HierarchyElement $parent = null;

    /**
     * Parse a JSON object to an element.
     *
     * @param object $object an object to parse
     *
     * @return void
     */
    public function parse(object $object)
    {
        if (isset($object->meta) && isset($object->meta->title)) {
            $this->title = $object->meta->title->content ?? $object->meta->title;
        }

        if (!isset($object->content) || !is_array($object->content)) {
            return;
        }

        foreach ($object->content as $key => $item) {
            if ($item->element === 'copy') {
                $this->description = $item->content;
                unset($object->content[$key]);
            }
        }

        if ($object->content !== null && $object->content !== []) {
            $object->content = array_slice($object->content, 0);
        }
    }

    /**
     * Get a linkable HREF.
     *
     * @return string
     */
    public function get_href(): string
    {
        $separator = '-';
        $prep      = ($this->parent !== null) ? $this->parent->get_href() . $separator : '';

        return $prep . str_replace(' ', '-', strtolower($this->title));
    }
}
