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
     * Add a struct dependency
     *
     * @param string $object Name of the struct to add
     * @internal param string $name Name of the type
     */
    public function add_struct($object)
    {
        echo "<pre>";
        var_dump($object);
        echo "</pre>";
    }

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
        foreach ($object->content as $key => $item) {
            switch ($item->element) {
                case 'resource':
                    $resource = new Resource($this);
                    $this->children[] = $resource->parse($item);
                    break;
                case 'dataStructure':
                    echo "<pre>";
                    $deps = [];
                    $struct = new DataStructureElement([$this, 'add_struct']);
                    $struct = $struct->parse($item, $deps);
                    $struct_array = ['struct' => $struct, 'deps' => $deps];
                    var_dump($deps);
                    if (isset($item->content[0]->meta->id)) {
                        $this->structures[$item->content[0]->meta->id] = $struct_array;
                    } else {
                        $this->structures[] = $struct_array;
                    }

                    echo "</pre>";
                    break;
                default:
                    continue;
                    break;
            }
        }

        return $this;
    }
}