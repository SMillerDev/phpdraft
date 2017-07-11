<?php
/**
 * This file contains the Category.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

use PHPDraft\Model\Elements\ObjectStructureElement;

/**
 * Class Category
 */
class Category extends HierarchyElement
{
    /**
     * API Structure element
     *
     * @var ObjectStructureElement[]
     */
    public $structures = [];

    /**
     * Fill class values based on JSON object
     *
     * @param \stdClass $object JSON object
     *
     * @return $this self-reference
     */
    function parse($object)
    {
        parent::parse($object);
        foreach ($object->content as $item) {
            switch ($item->element) {
                case 'resource':
                    $resource         = new Resource($this);
                    $this->children[] = $resource->parse($item);
                    break;
                case 'dataStructure':
                    $deps         = [];
                    $struct       = new ObjectStructureElement();
                    $struct->deps = $deps;
                    $struct->parse($item, $deps);

                    if (is_array($item->content) && isset($item->content[0]->meta->id))
                    {
                        $this->structures[$item->content[0]->meta->id] = $struct;
                    } else {
                        $this->structures[] = $struct;
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