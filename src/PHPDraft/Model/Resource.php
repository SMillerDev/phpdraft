<?php
/**
 * This file contains the Resource.php
 *
 * @package php-drafter\SOMETHING
 * @author Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

class Resource extends APIBlueprintElement
{
    /**
     * Location relative to the base URL
     *
     * @var string
     */
    public $href;

    /**
     * Resource constructor.
     *
     * @param Category $parent A reference to the parent object
     */
    public function __construct(&$parent)
    {
        $this->parent = $parent;
    }

    function parse($object)
    {
        parent::parse($object);

        if (isset($object->attributes)) $this->href = $object->attributes->href;

        foreach ($object->content as $key => $item)
        {
            if ($item->element === 'copy') continue;
            $transition = new Transition($this);
            array_push($this->children, $transition->parse($item));
        }

        return $this;
    }

}