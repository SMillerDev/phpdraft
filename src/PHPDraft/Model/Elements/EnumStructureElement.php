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
        $this->element = $object->element;

        $this->parse_common($object, $dependencies);

        $this->key   = $this->key ?? $object->content->content ?? null;
        $this->type  = $this->type ?? $object->content->element ?? null;

        if (!isset($object->content) && !isset($object->attributes)) {
            $this->value = $this->key;

            return $this;
        }

        if (isset($object->attributes->default)) {
            if (!in_array($object->attributes->default->content->element ?? '', self::DEFAULTS)) {
                $dependencies[] = $object->attributes->default->content->element;
            }
            $this->value = $object->attributes->default->content->content;
            $this->deps  = $dependencies;

            return $this;
        }

        if (isset($object->content)) {
            if (!in_array($object->content->element, self::DEFAULTS)) {
                $dependencies[] = $object->content->element;
            }
            $this->value = $object->content->content;
            $this->deps  = $dependencies;

            return $this;
        }

        foreach ($object->attributes->enumerations->content as $sub_item) {
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
        if (is_string($this->value)) {
            $type = $this->get_element_as_html($this->element);

            return '<tr><td>' . $this->key . '</td><td>' . $type . '</td><td>' . $this->description . '</td></tr>';
        }

        $return = '';
        foreach ($this->value as $value => $key) {
            $type = $type = $this->get_element_as_html($key);

            $item = empty($value) ? '' : " - <span class=\"example-value pull-right\">$value</span>";
            $return .= '<li class="list-group-item mdl-list__item">' . $type . $item . '</li>';
        }

        return '<ul class="list-group mdl-list">' . $return . '</ul>';
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
