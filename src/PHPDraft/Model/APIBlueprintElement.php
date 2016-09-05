<?php
/**
 * This file contains the APIBlueprintElement class
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

use Michelf\Markdown;

abstract class APIBlueprintElement
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
     * @var APIBlueprintElement[]
     */
    public $children = [];

    /**
     * Parent Element
     *
     * @var APIBlueprintElement|NULL
     */
    protected $parent = NULL;

    /**
     * Parse a JSON object to an element
     *
     * @param \stdClass $object an object to parse
     *
     * @return void
     */
    function parse($object)
    {
        if (isset($object->meta) && isset($object->meta->title))
        {
            $this->title = $object->meta->title;
        }
        if (!isset($object->content))
        {
            return;
        }
        foreach ($object->content as $key => $item)
        {
            if ($item->element === 'copy')
            {
                $this->description = preg_replace('/(<\/?p>)?/', '', Markdown::defaultTransform($item->content), 2);
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
        $prep = ($this->parent !== NULL) ? $this->parent->get_href() . $seperator : '';

        return $prep . str_replace(' ', $seperator, strtolower($this->title));
    }
}