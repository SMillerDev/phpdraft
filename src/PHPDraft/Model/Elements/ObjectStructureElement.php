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

/**
 * Class ObjectStructureElement.
 */
class ObjectStructureElement extends BasicStructureElement
{

    private $object;

    /**
     * Unset object function.
     * @internal Only for tests
     */
    public function __clearForTest(){$this->object = NULL;}

    /**
     * Parse a JSON object to a data structure.
     *
     * @param object|null $object       An object to parse
     * @param array      $dependencies Dependencies of this object
     *
     * @return ObjectStructureElement self reference
     */
    public function parse(?object $object, array &$dependencies): StructureElement
    {
        $this->object = $object;
        if (empty($object) || !isset($object->element) || !(isset($object->content) || isset($object->meta) )) {
            return $this;
        }

        $this->element = $object->element;
        $this->parse_common($object, $dependencies);
        if (!isset($object->content) && isset($object->meta)) {
            return $this;
        }

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
                if ($carry === NULL) {return "$item->content ($item->element)"; }
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
     * @param mixed $value       APIB content
     * @param array $dependencies Object dependencies
     *
     * @return void
     */
    protected function parse_value_structure($value, array &$dependencies)
    {
        switch ($this->type) {
            case 'array':
                $struct = new ArrayStructureElement();
                break;
            case 'enum':
                $struct = new EnumStructureElement();
                break;
            default:
            case 'object':
                $value  = $value->content->value ?? null;
                $struct = $this->new_instance();
                break;
        }
        $this->value = $struct->parse($value, $dependencies);

        unset($struct);
        unset($value);
    }

    /**
     * Get a new instance of a class.
     *
     * @return ObjectStructureElement
     */
    protected function new_instance(): StructureElement
    {
        return new self();
    }

    /**
     * Parse content formed as an array.
     *
     * @param mixed $object       APIB content
     * @param array $dependencies Object dependencies
     *
     * @return void
     */
    protected function parse_array_content($object, array &$dependencies): void
    {
        foreach ($object->content as $value) {
            switch ($this->type){
                case 'enum':
                    $struct = new EnumStructureElement();
                    break;
                case 'array':
                    $struct = new ArrayStructureElement();
                    break;
                default:
                case 'object':
                    $struct = $this->new_instance();
                    break;
            }

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
        $options = array_merge(self::DEFAULTS,['member', 'select', 'option', 'ref', 'T', 'hrefVariables']);
        if (!empty($this->element) && !in_array($this->element, $options)) {
            $this->description = '<p>Inherits from <a href="#object-' . strtolower($this->element) . '">' . $this->element . '</a></p>' . $this->description;
        }

        if (is_array($this->value)) {
            $return = '<table class="table table-striped mdl-data-table mdl-js-data-table ">';
            foreach ($this->value as $object) {
                if (is_string($object) || is_subclass_of(get_class($object), BasicStructureElement::class)) {
                    $return .= $object;
                }
            }

            $return .= '</table>';

            return $this->description . $return;
        }

        if ($this->ref !== null) {
            return '<p>Inherits from <a href="#object-' . strtolower($this->ref) . '">' . $this->ref . '</a></p>' . $this->description;
        }

        if ($this->value === null && $this->key === null && $this->description !== null) {
            return "<div class='description clearfix'>$this->description</div>";
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
        if ($this->type === NULL) {
            return $value;
        }
        if (!in_array($this->type, self::DEFAULTS)) {
            $type = '<a class="code" href="#object-' . str_replace(
                ' ',
                '-',
                strtolower($this->type)
            ) . '">' . $this->type . '</a>';
        } else {
            $type = '<code>' . $this->type . '</code>';
        }
        $variable = '';
        if ($this->is_variable) {
            $variable = '<span class="fas fa-info variable-info" data-toggle="tooltip" data-placement="top" title="This is a variable key"></span>';
        }

        return '<tr>' .
            '<td>' . '<span>' . $this->key . '</span>' . $variable . '</td>' .
            '<td>' . $type . '</td>' .
            '<td> <span class="status">' . $this->status . '</span></td>' .
            '<td>' . $this->description . '</td>' .
            '<td>' . $value . '</td>' .
            '</tr>';
    }
}
