<?php
/**
 * This file contains the DataStructureElement.php
 *
 * @package PHPDraft\Model\Elements
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements;

use Michelf\MarkdownExtra;
use PHPDraft\Model\StructureElement;

class DataStructureElement implements StructureElement
{

    /**
     * Object key
     *
     * @var string
     */
    public $key;
    /**
     * Object JSON type
     *
     * @var mixed
     */
    public $type;
    /**
     * Object description
     *
     * @var string
     */
    public $description;
    /**
     * Type of element
     *
     * @var string
     */
    public $element = null;
    /**
     * Object value
     *
     * @var mixed|DataStructureElement[]
     */
    public $value = null;
    /**
     * Object status (required|optional)
     *
     * @var string
     */
    public $status = '';
    /**
     * List of object dependencies
     *
     * @var string[]
     */
    public $deps;

    /**
     * Parse a JSON object to a data structure
     *
     * @param \stdClass $object       An object to parse
     * @param array     $dependencies Dependencies of this object
     *
     * @return DataStructureElement self reference
     */
    public function parse($object, &$dependencies)
    {
        if (empty($object) || !isset($object->content)) {
            return $this;
        }
        $this->element = $object->element;

        if (isset($object->content) && is_array($object->content)) {
            foreach ($object->content as $value) {
                if (in_array($this->element, ['object', 'dataStructure', 'hrefVariables'])) {
                    $struct = new DataStructureElement();
                } else {
                    $struct = new EnumStructureElement();
                }
                $this->value[] = $struct->parse($value, $dependencies);
            }
            unset($value);

            return $this;
        }


        $this->key         = $object->content->key->content;
        $this->type        = $object->content->value->element;
        $this->description = isset($object->meta->description) ? htmlentities($object->meta->description) : null;
        $this->status      =
            isset($object->attributes->typeAttributes) ? join(', ', $object->attributes->typeAttributes) : null;

        $this->description_as_html();

        if (!in_array($this->type, self::DEFAULTS)) {
            $dependencies[] = $this->type;
        }

        if ($this->type === 'object' || $this->type === 'array' || $this->type === 'enum' || !in_array($this->type,
                self::DEFAULTS)
        ) {
            $value = isset($object->content->value->content) ? $object->content->value : null;
            switch ($this->type) {
                case 'array':
                    $this->value = new ArrayStructureElement();

                    break;
                case 'enum':
                    $this->value = new EnumStructureElement();

                    break;
                case 'object':
                default:
                    $this->value = new DataStructureElement();
                    break;
            }
            $this->value = $this->value->parse($value, $dependencies);

            return $this;
        }

        if (isset($object->content->value->content)) {
            $this->value = $object->content->value->content;
        } elseif (isset($object->content->value->attributes->samples)) {
            $this->value = join(' | ', $object->content->value->attributes->samples);
        } else {
            $this->value = null;
        }

        return $this;
    }

    /**
     * Parse the description to HTML
     *
     * @return string
     */
    public function description_as_html()
    {
        $this->description = MarkdownExtra::defaultTransform($this->description);
    }

    /**
     * Print a string representation
     *
     * @return string
     */
    function __toString()
    {
        if ($this->value === null && $this->key === null) {
            return '<span class="example-value pull-right">{ ... }</span>';
        }

        if (is_array($this->value)) {
            $return = '<table class="table table-striped">';
            foreach ($this->value as $object) {
                if (get_class($object) === \stdClass::class) {
                    return json_encode($object);
                }
                if (is_string($object)
                    || get_class($object) === self::class
                    || get_class($object) === ArrayStructureElement::class
                    || get_class($object) === RequestBodyElement::class
                    || get_class($object) === EnumStructureElement::class
                ) {
                    $return .= $object;
                }
            }

            $return .= '</table>';

            return $return;
        }

        $type = (!in_array($this->type, self::DEFAULTS)) ?
            '<a class="code" href="#object-' . str_replace(' ', '-',
                strtolower($this->type)) . '">' . $this->type . '</a>' : '<code>' . $this->type . '</code>';

        if (is_null($this->value)) {
            $value = '';
        } else {
            if (is_object($this->value) && (self::class === get_class($this->value) || RequestBodyElement::class === get_class($this->value))) {
                $value = '<div class="sub-struct">' . $this->value . '</div>';
            } elseif (is_object($this->value) && (ArrayStructureElement::class === get_class($this->value))) {
                $value = '<div class="array-struct">' . $this->value . '</div>';
            } elseif (is_array($this->value) && (EnumStructureElement::class === get_class($this->value))) {
                $value = '<div class="enum-struct">' . $this->value . '</div>';
            } else {
                $value = '<span class="example-value pull-right">';
                if (is_bool($this->value)) {
                    $value .= ($this->value) ? 'true' : 'false';
                } else {
                    $value .= $this->value;
                }
                $value .= '</span>';
            }
        }

        $return =
            '<tr>' .
            '<td>' . '<span>' . $this->key . "</span>" . '</td>' .
            '<td>' . $type . '</td>' .
            '<td> <span class="status">' . $this->status . '</span></td>' .
            '<td><span>' . $this->description . '</span></td>' .
            '<td>' . $value . '</td>' .
            '</tr>';

        return $return;
    }

    function strval()
    {
        if (is_array($this->value)) {
            $key = rand(0, count($this->value));
            if (is_subclass_of($this->value[$key], StructureElement::class)) {
                return $this->value[$key]->strval();
            }

            return $this->value[$key];
        }

        return $this->value;
    }

}