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

use stdClass;

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
    public $title;

    /**
     * Description of the element.
     *
     * @var string
     */
    public $description;

    /**
     * Child elements.
     *
     * @var HierarchyElement[]
     */
    public $children = [];

    /**
     * Parent Element.
     *
     * @var HierarchyElement|null
     */
    protected $parent = null;

    /**
     * Parse a JSON object to an element.
     *
     * @param stdClass $object an object to parse
     *
     * @return void
     */
    public function parse(stdClass $object)
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
                continue;
            }
        }

        if (!empty($object->content)) {
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
