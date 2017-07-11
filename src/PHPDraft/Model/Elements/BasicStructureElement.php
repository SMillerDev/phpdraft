<?php
/**
 * Created by PhpStorm.
 * User: smillernl
 * Date: 13-7-17
 * Time: 11:07
 */

namespace PHPDraft\Model\Elements;


use Michelf\MarkdownExtra;

abstract class BasicStructureElement implements StructureElement
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
    public $element = NULL;
    /**
     * Object value
     *
     * @var mixed|ObjectStructureElement[]
     */
    public $value = NULL;
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
     * Parse a JSON object to a structure
     *
     * @param \stdClass $object       An object to parse
     * @param array     $dependencies Dependencies of this object
     *
     * @return StructureElement self reference
     */
    abstract public function parse($object, &$dependencies);

    /**
     * Print a string representation
     *
     * @return string
     */
    abstract public function __toString();

    /**
     * Get a new instance of a class
     *
     * @return self
     */
    abstract protected function new_instance();

    /**
     * Parse common fields to give context
     *
     * @param mixed $object       APIB object
     * @param array $dependencies Object dependencies
     *
     * @return void
     */
    protected function parse_common($object, &$dependencies)
    {
        $this->key         = $object->content->key->content;
        $this->type        = $object->content->value->element;
        $this->description = isset($object->meta->description) ? htmlentities($object->meta->description) : NULL;
        $this->status      =
            isset($object->attributes->typeAttributes) ? join(', ', $object->attributes->typeAttributes) : NULL;

        $this->description_as_html();

        if (!in_array($this->type, self::DEFAULTS))
        {
            $dependencies[] = $this->type;
        }
    }

    /**
     * Parse the description to HTML
     *
     * @return void
     */
    public function description_as_html()
    {
        $this->description = MarkdownExtra::defaultTransform($this->description);
    }

    /**
     * @return mixed|ObjectStructureElement|ObjectStructureElement[]
     */
    function string_value()
    {
        if (is_array($this->value))
        {
            $key = rand(0, count($this->value));
            if (is_subclass_of($this->value[$key], StructureElement::class))
            {
                return $this->value[$key]->string_value();
            }

            return $this->value[$key];
        }

        return $this->value;
    }
}