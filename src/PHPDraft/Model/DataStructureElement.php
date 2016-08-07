<?php
/**
 * This file contains the DataStructureElement.php
 *
 * @package php-drafter\SOMETHING
 * @author Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

class DataStructureElement
{
    public $defaults = ['boolean', 'string', 'number'];

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
     * Callback for data types
     * @var callable
     */
    protected $callback;

    /**
     * DataStructureElement constructor.
     * @param \stdClass $object   Object to parse
     * @param callable  $callback Call on object discovery
     */
    public function __construct($object = NULL, $callback = NULL)
    {
        $this->callback = $callback;
        if ($object !== NULL) $this->parse($object);
    }

    /**
     * Parse a JSON object to a data structure
     *
     * @param \stdClass $object An object to parse
     * @return DataStructureElement self reference
     */
    function parse($object)
    {
        if (empty($object)) return $this;
        $this->element = $object->element;
        if (is_array($object->content))
        {
            foreach ($object->content as $value)
            {
                $this->value[] = new DataStructureElement($value, $this->callback);
            }

            return $this;
        }

        $this->key         = $object->content->key->content;
        $this->type        = $object->content->value->element;
        $this->description = isset($object->meta->description) ? $object->meta->description : NULL;
        $this->status      = isset($object->attributes->typeAttributes[0]) ? $object->attributes->typeAttributes[0] : '';

        if (!is_null($this->callback) && !in_array($this->type, $this->defaults))
        {
            call_user_func($this->callback, $this->type);
        }

        if ($object->content->value->element === 'object')
        {
            $value       = isset($object->content->value->content) ? $object->content->value : NULL;
            $this->value = new DataStructureElement($value, $this->callback);
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
        if ($this->value == NULL && $this->key == NULL)
        {
            return '{ ... }';
        }

        return json_encode($this->value);
    }

}