<?php
declare(strict_types=1);

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
     * @param object|null $object       APIB Item to parse
     * @param array       $dependencies List of dependencies build
     *
     * @return self Self reference
     */
    public function parse(?object $object, array &$dependencies): StructureElement
    {
        $this->element = $object->element ?? 'array';

        $this->parse_common($object, $dependencies);

        if (!isset($object->content->value->content)) {
            $this->value = [];

            return $this;
        }

        foreach ($object->content->value->content as $sub_item) {
            if (!in_array($sub_item->element, self::DEFAULTS)) {
                $dependencies[] = $sub_item->element;
            }

            $key   = $sub_item->element ?? 'any';
            $value = $sub_item->content ?? NULL;
            $this->value[] = [$value => $key];
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

        if (!is_array($this->value)) {
            return '<span class="example-value pull-right">[ ]</span>';
        }

        foreach ($this->value as $item) {
            $value = key($item);
            $key = $item[$value];
            $type = (in_array($key, self::DEFAULTS)) ? "<code>$key</code>" : '<a href="#object-' . str_replace(
                ' ',
                '-',
                strtolower($key)
            ) . '">' . $key . '</a>';

            $value = empty($value) ? '' : " - <span class=\"example-value pull-right\">$value</span>";
            $return .= '<li class="list-group-item mdl-list__item">' . $type . $value . '</li>';
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
