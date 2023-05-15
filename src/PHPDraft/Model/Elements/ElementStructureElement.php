<?php

namespace PHPDraft\Model\Elements;

use Stringable;

class ElementStructureElement implements StructureElement
{
    /**
     * Object JSON type.
     *
     * @var string
     */
    public string $type;

    /**
     * Object description.
     *
     * @var string|null
     */
    public ?string $description = null;

    /**
     * Object value.
     *
     * @var mixed
     */
    public mixed $value = null;

    /**
     * Parse a JSON object to a structure.
     *
     * @param object|null $object       An object to parse
     * @param string[]    $dependencies Dependencies of this object
     *
     * @return self self reference
     */
    public function parse(?object $object, array &$dependencies): self
    {
        if (!in_array($object->element, self::DEFAULTS, true)) {
            $dependencies[] = $object->element;
        }

        $this->type  = $object->element;
        $this->value = $object->content ?? null;
        $this->description = $object->meta->description->content ?? null;

        return $this;
    }

    public function __toString(): string
    {
        $type = $this->get_element_as_html($this->type);

        $desc  = is_null($this->description) ? '' : " - <span class=\"description\">{$this->description}</span>";
        $value = is_null($this->value) ? '' : " - <span class=\"example-value pull-right\">{$this->value}</span>";
        return '<li class="list-group-item mdl-list__item">' . $type . $desc . $value . '</li>';
    }

    /**
     * Get a string representation of the value.
     *
     * @param bool $flat get a flat representation of the item.
     *
     * @return string
     */
    public function string_value(bool $flat = false)
    {
        if ($flat === true) {
            return $this->value;
        }

        return $this->__toString();
    }

    /**
     * Represent the element in HTML.
     *
     * @param string|null $element Element name
     *
     * @return string HTML string
     */
    protected function get_element_as_html(?string $element): string
    {
        if ($element === null) {
            return '<code>null</code>';
        }

        if (in_array($element, self::DEFAULTS, true)) {
            return '<code>' . $element . '</code>';
        }

        $link_name = str_replace(' ', '-', strtolower($element));
        return '<a class="code" title="' . $element . '" href="#object-' . $link_name . '">' . $element . '</a>';
    }
}
