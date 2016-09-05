<?php
/**
 * This file contains the DataStructureElement.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

use PHPDraft\Model\Elements\ArrayStructureElement;

class DataStructureElement
{
    /**
     * Default datatypes
     * @var array
     */
    const DEFAULTS = ['boolean', 'string', 'number', 'object', 'array'];
    /**
     * Object key
     * @var string
     */
    public $key;
    /**
     * Object JSON type
     * @var mixed
     */
    public $type;
    /**
     * Object description
     * @var string
     */
    public $description;
    /**
     * Type of element
     * @var string
     */
    public $element = NULL;
    /**
     * Object value
     * @var mixed|DataStructureElement[]
     */
    public $value = NULL;
    /**
     * Object status (required|optional)
     * @var string
     */
    public $status = '';
    /**
     * List of object dependencies
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
        if (empty($object) || !isset($object->content))
        {
            return $this;
        }
        $this->element = $object->element;
        if (isset($object->content) && is_array($object->content))
        {
            foreach ($object->content as $value)
            {
                $struct        = new DataStructureElement();
                $this->value[] = $struct->parse($value, $dependencies);
            }

            return $this;
        }

        $this->key         = $object->content->key->content;
        $this->type        = $object->content->value->element;
        $this->description = isset($object->meta->description) ? $object->meta->description : NULL;
        $this->status      =
            isset($object->attributes->typeAttributes) ? join(', ', $object->attributes->typeAttributes) : NULL;

        if (!in_array($this->type, self::DEFAULTS))
        {
            $dependencies[] = $this->type;
        }

        if ($this->type === 'object' || $this->type === 'array')
        {
            $value = isset($object->content->value->content) ? $object->content->value : NULL;
            if ($this->type === 'array')
            {
                $this->value = new ArrayStructureElement();
            }
            else
            {
                $this->value = new DataStructureElement();
            }
            $this->value = $this->value->parse($value, $dependencies);

            return $this;
        }

        $this->value = isset($object->content->value->content) ? $object->content->value->content : NULL;

        return $this;
    }

    /**
     * Print a string representation
     *
     * @return string
     */
    function __toString()
    {
        if ($this->value === NULL && $this->key === NULL)
        {
            return '<span class="example-value pull-right">{ ... }</span>';
        }

        if (is_array($this->value))
        {
            $return = '<table class="table table-striped">';
            foreach ($this->value as $object)
            {
                if (is_string($object) || get_class($object) === self::class || get_class($object) === ArrayStructureElement::class)
                {
                    $return .= $object;
                }
            }

            $return .= '</table>';

            return $return;
        }

        $type = (!in_array($this->type, self::DEFAULTS)) ?
            '<a class="code" href="#object-' . $this->type . '">' . $this->type . '</a>' : '<code>' . $this->type . '</code>';

        if (empty($this->value))
        {
            $value = '';
        }
        else
        {
            if (is_object($this->value) && self::class === get_class($this->value))
            {
                $value = '<div class="sub-struct">' . $this->value . '</div>';
            }
            elseif (is_object($this->value) && (ArrayStructureElement::class === get_class($this->value)))
            {
                $value = '<div class="array-struct">' . $this->value . '</div>';
            }
            else
            {
                $value = '<span class="example-value pull-right">' . $this->value . '</span>';
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

}