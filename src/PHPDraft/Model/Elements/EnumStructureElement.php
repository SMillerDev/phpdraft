<?php
/**
 * This file contains the ${FILE_NAME}
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements;

use Michelf\MarkdownExtra;
use PHPDraft\Model\StructureElement;

class EnumStructureElement extends ObjectStructureElement implements StructureElement
{
    /**
     * Parse an array object
     *
     * @param \stdClass $object       APIb Item to parse
     * @param array     $dependencies List of dependencies build
     *
     * @return $this
     */
    public function parse($object, &$dependencies)
    {
        $this->element = (isset($object->element)) ? $object->element : 'array';

        $this->parse_common($object, $dependencies);

        if(!isset($object->content->value->content))
        {
            $this->value = [];
            return $this;
        }

        foreach ($object->content->value->content as $sub_item)
        {
            if (!in_array($sub_item->element, self::DEFAULTS))
            {
                $dependencies[] = $sub_item->element;
            }

            $this->value[$sub_item->content] = (isset($sub_item->element)) ? $sub_item->element : '';
        }

        $this->deps = $dependencies;

        return $this;
    }

    /**
     * Provide HTML representation
     *
     * @return string
     */
    function __toString()
    {
        $return = '<ul class="list-group">';

        if (!is_array($this->value))
        {
            return '<span class="example-value pull-right">//list of options</span>';
        }

        foreach ($this->value as $key=>$item) {
            $type = (in_array($item, self::DEFAULTS)) ? $key : '<a href="#object-' . str_replace(' ', '-',
                    strtolower($item)) . '">' . $key . '</a>';

            $return .= '<li class="list-group-item">' . $type . '</li>';
        }

        $return .= '</ul>';

        return $return;
    }
}