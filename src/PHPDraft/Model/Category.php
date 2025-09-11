<?php

declare(strict_types=1);

/**
 * This file contains the Category.php.
 *
 * @package PHPDraft\Model
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

use PHPDraft\Model\Elements\BasicStructureElement;
use PHPDraft\Model\Elements\ObjectStructureElement;

/**
 * Class Category.
 */
class Category extends HierarchyElement
{
    /**
     * API Structure element.
     *
     * @var BasicStructureElement[]
     */
    public array $structures = [];

    /**
     * Fill class values based on JSON object.
     *
     * @param object $object JSON object
     *
     * @return self self-reference
     */
    public function parse(object $object): self
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
                    $struct       = (new ObjectStructureElement())->get_class($item->content->element);
                    $struct->deps = $deps;
                    $struct->parse($item->content, $deps);

                    if (isset($item->content->content) && is_array($item->content->content) && isset($item->content->content[0]->meta->id)) {
                        $this->structures[$item->content->content[0]->meta->id] = $struct;
                    } elseif (isset($item->content->meta->id->content)) {
                        $this->structures[$item->content->meta->id->content] = $struct;
                    } else {
                        $this->structures[] = $struct;
                    }

                    break;
                default:
                    continue 2;
            }
        }

        return $this;
    }
}
