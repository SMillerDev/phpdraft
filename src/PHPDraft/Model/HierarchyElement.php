<?php
/**
 * This file contains the APIBlueprintElement class
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

use Michelf\MarkdownExtra;

/**
 * Class HierarchyElement
 */
abstract class HierarchyElement
{
    /**
     * Title of the element
     *
     * @var string
     */
    public $title;

    /**
     * Description of the element
     *
     * @var string
     */
    public $description;

    /**
     * Child elements
     *
     * @var HierarchyElement[]
     */
    public $children = [];

    /**
     * Parent Element
     *
     * @var HierarchyElement|NULL
     */
    protected $parent = NULL;

    /**
     * Parse a JSON object to an element
     *
     * @param \stdClass $object an object to parse
     *
     * @return void
     */
    public function parse($object)
    {
        if (isset($object->meta) && isset($object->meta->title))
        {
            $this->title = $object->meta->title;
        }

        if (!isset($object->content) || !is_array($object->content))
        {
            return;
        }

        foreach ($object->content as $key => $item)
        {
            if ($item->element === 'copy')
            {
                $this->description = preg_replace('/(<\/?p>)/', '', MarkdownExtra::defaultTransform($item->content), 2);
                unset($object->content[$key]);
                continue;
            }
        }

        if (!empty($object->content))
        {
            $object->content = array_slice($object->content, 0);
        }

    }


    /**
     * Get a linkable HREF
     *
     * @return string
     */
    public function get_href()
    {
        $seperator = '-';
        $prep      = ($this->parent !== NULL) ? $this->parent->get_href() . $seperator : '';

        return $prep . str_replace(' ', '-', strtolower($this->title));
    }
}