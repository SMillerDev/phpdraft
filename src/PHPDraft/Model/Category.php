<?php
/**
 * This file contains the Category.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

class Category extends APIBlueprintElement
{
    /**
     * API Structure element
     * @var DataStructureElement[]
     */
    public $structures = [];

    /**
     * Parse the category
     *
     * @param \stdClass $object
     *
     * @return $this
     */
    function parse($object)
    {
        parent::parse($object);
        foreach ($object->content as $key => $item)
        {
            switch ($item->element)
            {
                case 'resource':
                    $resource         = new Resource($this);
                    $this->children[] = $resource->parse($item);
                    break;
                case 'dataStructure':
                    $struct = new DataStructureElement();
                    if (isset($item->content[0]->meta->id))
                    {
                        $this->structures[$item->content[0]->meta->id] = $struct->parse($item);
                    } else
                    {
                        $this->structures[] = $struct->parse($item);
                    }

                    break;
                default:
                    continue;
                    break;
            }
        }

        return $this;
    }
}