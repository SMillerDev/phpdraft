<?php
declare(strict_types=1);

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
     * @param object|null $object       APIB Item to parse
     * @param array       $dependencies List of dependencies build
     *
     * @return $this
     */
    public function parse(?object $object, array &$dependencies): StructureElement
    {
        $this->element = (isset($object->element)) ? $object->element : 'enum';

        $this->parse_common($object, $dependencies);

        $this->key   = $this->key ?? $object->content ?? 'UNKNOWN';
        $this->type  = $this->type ?? $object->element;

        if (!isset($object->content->value->content)) {
            $this->value = $this->key;

            return $this;
        }

        $enumerations = $object->content->value->attributes->enumerations->content ?? $object->content->value->content;
        foreach ($enumerations as $sub_item) {
            if (!in_array($sub_item->element, self::DEFAULTS)) {
                $dependencies[] = $sub_item->element;
            }

            $this->value[$sub_item->content] = $sub_item->element ?? '';
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

        foreach ($this->value as $value => $key) {
            $type = (in_array($key, self::DEFAULTS)) ? "<code>$key</code>" : '<a href="#object-' . str_replace(
                ' ',
                '-',
                strtolower($key)
            ) . '">' . $key . '</a>';

            $item = empty($value) ? '' : " - <span class=\"example-value pull-right\">$value</span>";
            $return .= '<li class="list-group-item mdl-list__item">' . $type . $item . '</li>';
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
