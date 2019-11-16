<?php
/**
 * Created by PhpStorm.
 * User: smillernl
 * Date: 13-7-17
 * Time: 11:07.
 */

namespace PHPDraft\Model\Elements;

use Michelf\MarkdownExtra;
use stdClass;

abstract class BasicStructureElement implements StructureElement
{
    /**
     * Object key.
     *
     * @var string
     */
    public $key;
    /**
     * Object JSON type.
     *
     * @var mixed
     */
    public $type;
    /**
     * Object description.
     *
     * @var string
     */
    public $description;
    /**
     * Type of element.
     *
     * @var string
     */
    public $element = NULL;
    /**
     * Object value.
     *
     * @var mixed|ObjectStructureElement[]
     */
    public $value = NULL;
    /**
     * Object status (required|optional).
     *
     * @var string
     */
    public $status = '';
    /**
     * List of object dependencies.
     *
     * @var string[]
     */
    public $deps;

    /**
     * Parse a JSON object to a structure.
     *
     * @param stdClass $object       An object to parse
     * @param array    $dependencies Dependencies of this object
     *
     * @return StructureElement self reference
     */
    abstract public function parse($object, array &$dependencies): StructureElement;

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
     * @param mixed $object       APIB object
     * @param array $dependencies Object dependencies
     *
     * @return void
     */
    protected function parse_common(stdClass $object, array &$dependencies): void
    {
        $this->key          = $object->content->key->content ?? NULL;
        $this->type         = $object->content->value->element ?? NULL;
        $this->description  = NULL;
        if (isset($object->meta->description->content)) {
            $this->description = htmlentities($object->meta->description->content);
        } elseif (isset($object->meta->description)) {
            $this->description = htmlentities($object->meta->description);
        }

        $this->status  = NULL;
        if (isset($object->attributes->typeAttributes->content)) {
            $data = array_map(function ($item) {
                return $item->content;
            }, $object->attributes->typeAttributes->content);
            $this->status = join(', ', $data);
        } elseif (isset($object->attributes->typeAttributes)) {
            $this->status = join(', ', $object->attributes->typeAttributes);
        }

        $this->description_as_html();

        if (!in_array($this->type, self::DEFAULTS)) {
            $dependencies[] = $this->type;
        }
    }

    /**
     * Parse the description to HTML.
     *
     * @return void
     */
    public function description_as_html(): void
    {
        $this->description = MarkdownExtra::defaultTransform($this->description);
    }

    /**
     * Get a string representation of the value.
     *
     * @return mixed|ObjectStructureElement|ObjectStructureElement[]
     */
    public function string_value()
    {
        if (is_array($this->value)) {
            $key = rand(0, count($this->value));
            if (is_subclass_of($this->value[$key], StructureElement::class)) {
                return $this->value[$key]->string_value();
            }

            return $this->value[$key];
        }

        return $this->value;
    }
}
