<?php
/**
 * This file contains the ArrayStructureElement.php.
 *
 * @package PHPDraft\Model\Elements
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements;

/**
 * Class ArrayStructureElement.
 */
class ArrayStructureElement extends BasicStructureElement
{
    /**
     * Parse an array object.
     *
     * @param \stdClass $object       APIb Item to parse
     * @param array     $dependencies List of dependencies build
     *
     * @return self Self reference
     */
    public function parse($object, &$dependencies)
    {
        $this->element = (isset($object->element)) ? $object->element : 'array';

        $this->parse_common($object, $dependencies);

        if (!isset($object->content->value->content)) {
            $this->value = [];

            return $this;
        }

        foreach ($object->content->value->content as $sub_item) {
            if (!in_array($sub_item->element, self::DEFAULTS)) {
                $dependencies[] = $sub_item->element;
            }

            $this->value[] = (isset($sub_item->element)) ? $sub_item->element : '';
        }

        $this->deps = $dependencies;

        return $this;
    }

    /**
     * Provide HTML representation.
     *
     * @return string
     */
    public function __toString()
    {
        $return = '<ul class="list-group mdl-list">';

        if (!is_array($this->value)) {
            return '<span class="example-value pull-right">[ ]</span>';
        }

        foreach ($this->value as $item) {
            $type = (in_array($item, self::DEFAULTS)) ? $item : '<a href="#object-' . str_replace(' ', '-',
                        strtolower($item)) . '">' . $item . '</a>';

            $return .= '<li class="list-group-item mdl-list__item">' . $type . '</li>';
        }

        $return .= '</ul>';

        return $return;
    }

    /**
     * Get a new instance of a class.
     *
     * @return self
     */
    protected function new_instance()
    {
        return new self();
    }
}
