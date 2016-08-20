<?php
/**
 * This file contains the DataStructureElement.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

class DataStructureElement
{
    /**
     * Object key
     * @var string
     */
    public $key;
    /**
     * Object JSON type
     * @var string
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
     * Unreported datatypes
     * @var array
     */
    protected $defaults = ['boolean', 'string', 'number', 'object', 'array'];

    /**
     * Parse a JSON object to a data structure
     *
     * @param \stdClass $object       An object to parse
     * @param array     $dependencies Dependencies of this object
     *
     * @return DataStructureElement self reference
     */
    function parse($object, &$dependencies)
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
                $struct        = new DataStructureElement($this->callback);
                $this->value[] = $struct->parse($value, $dependencies);
            }

            return $this;
        }

        $this->key         = $object->content->key->content;
        $this->type        = $object->content->value->element;
        $this->description = isset($object->meta->description) ? $object->meta->description : NULL;
        $this->status      =
            isset($object->attributes->typeAttributes[0]) ? $object->attributes->typeAttributes[0] : NULL;

        if (!in_array($this->type, $this->defaults))
        {
            $dependencies[] = $this->type;
        }

        if ($this->type === 'object')
        {
            $value       = isset($object->content->value->content) ? $object->content->value : NULL;
            $this->value = new DataStructureElement($this->callback);
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
            return '{ ... }';
        }

        return json_encode($this->value);
    }

}