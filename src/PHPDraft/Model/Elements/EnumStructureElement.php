<?php

/**
 * This file contains the ${FILE_NAME}.
 *
 * @package PHPDraft\Model
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements;

class EnumStructureElement extends BasicStructureElement
{
    /**
     * Parse an array object.
     *
     * @param object $object       APIB Item to parse
     * @param array  $dependencies List of dependencies build
     *
     * @return $this
     */
    public function parse(object $object, array &$dependencies): StructureElement
    {
        $this->element = (isset($object->element)) ? $object->element : 'enum';

        $this->parse_common($object, $dependencies);

        $this->key   = is_null($this->key) ? $object->content : $this->key;
        $this->type  = is_null($this->type) ? $object->element : $this->type;

        if (!isset($object->content->value->content)) {
            $this->value = $this->key;

            return $this;
        }

        $enumerations = $object->content->value->attributes->enumerations->content ?? $object->content->value->content;
        foreach ($enumerations as $sub_item) {
            if (!in_array($sub_item->element, self::DEFAULTS)) {
                $dependencies[] = $sub_item->element;
            }

            $this->value[$sub_item->content] = (isset($sub_item->element)) ? $sub_item->element : '';
        }

        $this->deps = $dependencies;

        return $this;
    }

    /**
     * Provide HTML representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        $return = '<ul class="list-group mdl-list">';

        if (is_string($this->value)) {
            $type = (in_array($this->element, self::DEFAULTS)) ? $this->element : '<a href="#object-' . str_replace(
                ' ',
                '-',
                strtolower($this->element)
            ) . '">' . $this->element . '</a>';

            return '<tr><td>' . $this->key . '</td><td><code>' . $type . '</code></td><td>' . $this->description . '</td></tr>';
        }

        if (!is_array($this->value)) {
            return '<span class="example-value pull-right">//list of options</span>';
        }

        foreach ($this->value as $key => $item) {
            $type = (in_array($item, self::DEFAULTS)) ? $key : '<a href="#object-' . str_replace(
                ' ',
                '-',
                strtolower($item)
            ) . '">' . $key . '</a>';

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
    protected function new_instance(): StructureElement
    {
        return new self();
    }
}
