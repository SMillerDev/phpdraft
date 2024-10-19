<?php

declare(strict_types=1);

/**
 * This file contains the ObjectStructureElement.php.
 *
 * @package PHPDraft\Model\Elements
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements;

use Michelf\MarkdownExtra;

/**
 * Class ObjectStructureElement.
 */
class ObjectStructureElement extends BasicStructureElement
{
    /**
     * Object representation before parsing
     * @var object|null
     * @phpstan-ignore-next-line
     */
    private ?object $object;

    /**
     * Unset object function.
     * @internal Only for tests
     */
    public function __clearForTest(): void
    {
        $this->object = null;
    }

    /**
     * Parse a JSON object to a data structure.
     *
     * @param object|null $object       An object to parse
     * @param string[]    $dependencies Dependencies of this object
     *
     * @return ObjectStructureElement self reference
     */
    public function parse(?object $object, array &$dependencies): StructureElement
    {
        $this->object = $object;
        if (is_null($object) || !isset($object->element) || !(isset($object->content) || isset($object->meta) )) {
            return $this;
        }

        $this->element = $object->element;
        $this->parse_common($object, $dependencies);

        if (isset($object->content) && is_array($object->content)) {
            $this->parse_array_content($object, $dependencies);
            return $this;
        }

        if (in_array($this->type, ['object', 'array', 'enum'], true) || !in_array($this->type, self::DEFAULTS, true)) {
            $this->parse_value_structure($object, $dependencies);

            return $this;
        }

        if (isset($object->content->value->content)) {
            $this->value = $object->content->value->content;
        } elseif (isset($object->content->value->attributes->samples)) {
            $this->value = array_reduce($object->content->value->attributes->samples->content, function ($carry, $item) {
                if ($carry === null) {
                    return "$item->content ($item->element)";
                }
                return "$carry | $item->content ($item->element)";
            });
        } else {
            $this->value = null;
        }

        return $this;
    }

    /**
     * Parse $this->value as a structure based on given content.
     *
     * @param object   $object       APIB content
     * @param string[] $dependencies Object dependencies
     *
     * @return void
     */
    protected function parse_value_structure(object $object, array &$dependencies)
    {
        if (isset($object->content->content) || in_array($this->element, ['boolean', 'string', 'number', 'ref'], true)) {
            return;
        }

        $value  = $object->content->value ?? $object;
        $type   = in_array($this->element, ['member'], true) ? $this->type : $this->element;
        $struct = $this->get_class($type);

        $this->value = $struct->parse($value, $dependencies);

        unset($struct);
        unset($value);
    }

    /**
     * Get a new instance of a class.
     *
     * @return ObjectStructureElement
     */
    protected function new_instance(): self
    {
        return new self();
    }

    /**
     * Parse content formed as an array.
     *
     * @param object   $object       APIB content
     * @param string[] $dependencies Object dependencies
     *
     * @return void
     */
    protected function parse_array_content(object $object, array &$dependencies): void
    {
        foreach ($object->content as $value) {
            $type   = $this->element === 'member' ? $this->type : $this->element;
            $struct = $this->get_class($type);

            $this->value[] = $struct->parse($value, $dependencies);
            unset($struct);
        }

        unset($value);
    }

    /**
     * Print a string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        if (is_array($this->value)) {
            $return = '';
            foreach ($this->value as $object) {
                if (is_string($object) || is_subclass_of(get_class($object), StructureElement::class)) {
                    $return .= $object;
                }
            }

            return "<table class=\"table table-striped mdl-data-table mdl-js-data-table \">$return</table>";
        }

        if ($this->value === null && $this->key === null && $this->description !== null) {
            return '';
        }

        if ($this->value === null && $this->key === null && $this->description === null) {
            return '<span class="example-value pull-right">{  }</span>';
        }

        if (is_null($this->value)) {
            return $this->construct_string_return('');
        }

        if (is_object($this->value) && (self::class === get_class($this->value) || RequestBodyElement::class === get_class($this->value))) {
            return $this->construct_string_return('<div class="sub-struct">' . $this->value . '</div>');
        }

        if (is_object($this->value) && (ArrayStructureElement::class === get_class($this->value))) {
            return $this->construct_string_return('<div class="array-struct">' . $this->value . '</div>');
        }

        if (is_object($this->value) && (EnumStructureElement::class === get_class($this->value))) {
            return $this->construct_string_return('<div class="enum-struct">' . $this->value . '</div>');
        }

        $value = '<span class="example-value pull-right">';
        if (is_bool($this->value)) {
            $value .= ($this->value) ? 'true' : 'false';
        } else {
            $value .= $this->value;
        }

        $value .= '</span>';

        return $this->construct_string_return($value);
    }

    /**
     * Create an HTML return.
     *
     * @param string $value value to display
     *
     * @return string
     */
    protected function construct_string_return(string $value): string
    {
        if ($this->type === null) {
            return $value;
        }

        $type     = $this->get_element_as_html($this->type);
        $variable = '';
        if ($this->is_variable === true) {
            $link_name = str_replace(' ', '-', strtolower($this->key->type));
            $tooltip = 'This is a variable key of type &quot;' . $this->key->type . '&quot;';
            $variable = '<a class="variable-key" title="' . $this->key->type . '" href="#object-' . $link_name . '"><span class="fas fa-info variable-info" data-toggle="tooltip" data-placement="top" data-tooltip="' . $tooltip . '" title="' . $tooltip . '"></span></a>';
        }
        $desc = '';
        if ($this->description !== null) {
            $desc = MarkdownExtra::defaultTransform($this->description);
        }

        $status_string = join(', ', $this->status);
        return "<tr><td><span>{$this->key->value}</span>{$variable}</td><td>{$type}</td><td> <span class=\"status\">{$status_string}</span></td><td>{$desc}</td><td>{$value}</td></tr>";
    }
}
