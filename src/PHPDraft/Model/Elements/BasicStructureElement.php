<?php

declare(strict_types=1);

/**
 * Basic structure element
 */

namespace PHPDraft\Model\Elements;

abstract class BasicStructureElement implements StructureElement
{
    /**
     * Object key.
     *
     * @var ElementStructureElement|null
     */
    public ?ElementStructureElement $key = null;
    /**
     * Object JSON type.
     *
     * @var string|null
     */
    public ?string $type;
    /**
     * Object description.
     *
     * @var string|null
     */
    public ?string $description = null;
    /**
     * Type of element.
     *
     * @var string|null
     */
    public ?string $element = null;
    /**
     * Object value.
     *
     * @var mixed
     */
    public $value = null;
    /**
     * Object status (required|optional).
     *
     * @var string|null
     */
    public ?string $status = '';
    /**
     * Parent structure.
     *
     * @var string|null
     */
    public ?string $ref;
    /**
     * Is variable.
     *
     * @var bool
     */
    public bool $is_variable = false;
    /**
     * List of object dependencies.
     *
     * @var string[]|null
     */
    public ?array $deps;

    /**
     * Parse a JSON object to a structure.
     *
     * @param object|null $object       An object to parse
     * @param string[]    $dependencies Dependencies of this object
     *
     * @return StructureElement self reference
     */
    abstract public function parse(?object $object, array &$dependencies): StructureElement;

    /**
     * Print a string representation.
     *
     * @return string
     */
    abstract public function __toString(): string;

    /**
     * Get a new instance of a class.
     *
     * @return self
     */
    abstract protected function new_instance(): StructureElement;

    /**
     * Parse common fields to give context.
     *
     * @param object   $object       APIB object
     * @param string[] $dependencies Object dependencies
     *
     * @return void
     */
    protected function parse_common(object $object, array &$dependencies): void
    {
        $this->key = null;
        if (isset($object->content->key)) {
            $key = new ElementStructureElement();
            $key->parse($object->content->key, $dependencies);
            $this->key = $key;
        }

        $this->type = $object->content->value->element
            ?? $object->meta->title->content
            ?? $object->meta->id->content
            ?? null;
        $this->description  = null;
        if (isset($object->meta->description->content)) {
            $this->description = htmlentities($object->meta->description->content);
        } elseif (isset($object->meta->description)) {
            $this->description = htmlentities($object->meta->description);
        }
        if ($this->description !== null) {
            $encoded           = htmlentities($this->description, ENT_COMPAT, 'ISO-8859-1', false);
            $this->description = $encoded;
        }

        $this->ref = null;
        if ($this->element === 'ref') {
            $this->ref = $object->content;
        }

        $this->is_variable = $object->attributes->variable->content ?? false;

        $this->status  = null;
        if (isset($object->attributes->typeAttributes->content)) {
            $data = array_map(function ($item) {
                return $item->content;
            }, $object->attributes->typeAttributes->content);
            $this->status = join(', ', $data);
        } elseif (isset($object->attributes->typeAttributes)) {
            $this->status = join(', ', $object->attributes->typeAttributes);
        }

        if (!in_array($this->type, self::DEFAULTS) && $this->type !== null) {
            $dependencies[] = $this->type;
        }
    }

    /**
     * Represent the element in HTML.
     *
     * @param string $element Element name
     *
     * @return string HTML string
     */
    protected function get_element_as_html(string $element): string
    {
        if (in_array($element, self::DEFAULTS)) {
            return '<code>' . $element . '</code>';
        }

        $link_name = str_replace(' ', '-', strtolower($element));
        return '<a class="code" title="' . $element . '" href="#object-' . $link_name . '">' . $element . '</a>';
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
        if (is_array($this->value)) {
            $value_key = rand(0, count($this->value));
            if (is_subclass_of($this->value[$value_key], StructureElement::class) && $flat === false) {
                return $this->value[$value_key]->string_value($flat);
            }

            return $this->value[$value_key];
        }

        if (is_subclass_of($this->value, BasicStructureElement::class) && $flat === true) {
            return is_array($this->value->value) ? array_keys($this->value->value)[0] : $this->value->value;
        }
        return $this->value;
    }

    /**
     * Get what element to parse with.
     *
     * @param string $element The string to parse.
     *
     * @return BasicStructureElement The element to parse to
     */
    public function get_class(string $element): BasicStructureElement
    {
        switch ($element) {
            default:
            case 'object':
                $struct = $this->new_instance();
                break;
            case 'array':
                $struct = new ArrayStructureElement();
                break;
            case 'enum':
                $struct = new EnumStructureElement();
                break;
        }

        return $struct;
    }
}
