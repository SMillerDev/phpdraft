<?php
/**
 * This file contains the ObjectStructureElement.php
 *
 * @package PHPDraft\Model\Elements
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements;

use Michelf\MarkdownExtra;
use PHPDraft\Model\StructureElement;

/**
 * Class ObjectStructureElement
 */
class ObjectStructureElement implements StructureElement
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
     * Parse a JSON object to a data structure
     *
     * @param \stdClass $object       An object to parse
     * @param array     $dependencies Dependencies of this object
     *
     * @return ObjectStructureElement self reference
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
            $this->parse_array_content($object, $dependencies);

            return $this;
        }

        $this->parse_common($object, $dependencies);

        if (in_array($this->type, ['object', 'array', 'enum'], TRUE) || !in_array($this->type, self::DEFAULTS, TRUE))
        {
            $this->parse_value_structure($object, $dependencies);
            return $this;
        }

        if (isset($object->content->value->content))
        {
            $this->value = $object->content->value->content;
        } elseif (isset($object->content->value->attributes->samples))
        {
            $this->value = join(' | ', $object->content->value->attributes->samples);
        } else {
            $this->value = NULL;
        }

        return $this;
    }

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
        $this->status      = isset($object->attributes->typeAttributes) ? join(', ', $object->attributes->typeAttributes) : NULL;

        $this->description_as_html();

        if (!in_array($this->type, self::DEFAULTS))
        {
            $dependencies[] = $this->type;
        }
    }

    /**
     * Parse $this->value as a structure based on given content
     *
     * @param mixed $object       APIB content
     * @param array $dependencies Object dependencies
     *
     * @return void
     */
    protected function parse_value_structure($object, &$dependencies)
    {
        switch ($this->type) {
            case 'array':
                $struct      = new ArrayStructureElement();
                $this->value = $struct->parse($object, $dependencies);
                break;
            case 'object':
            default:
                $value  = isset($object->content->value->content) ? $object->content->value->content : NULL;
                $struct = new ObjectStructureElement();

                $this->value = $struct->parse($value, $dependencies);
                break;
        }

        unset($struct);
        unset($value);
    }

    /**
     * Parse content formed as an array
     *
     * @param mixed $object       APIB content
     * @param array $dependencies Object dependencies
     *
     * @return void
     */
    protected function parse_array_content($object, &$dependencies)
    {
        foreach ($object->content as $value)
        {
            if ($this->element === 'enum')
            {
                $struct = new EnumStructureElement();
            } else {
                $struct = new ObjectStructureElement();
            }

            $this->value[] = $struct->parse($value, $dependencies);
            unset($struct);
        }

        unset($value);
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
        if ($this->value === NULL && $this->key === NULL)
        {
            return '<span class="example-value pull-right">{  }</span>';
        }

        if (is_array($this->value))
        {
            $return = '<table class="table table-striped">';
            foreach ($this->value as $object) {
                if (is_string($object)
                    || in_array(get_class($object), [
                        ObjectStructureElement::class,
                        ArrayStructureElement::class,
                        EnumStructureElement::class,
                        RequestBodyElement::class,
                    ], TRUE)
                )
                {
                    $return .= $object;
                }
            }

            $return .= '</table>';

            return $return;
        }



        if (is_null($this->value))
        {
            return $this->construct_string_return('');
        }

        if (is_object($this->value) && (self::class === get_class($this->value) || RequestBodyElement::class === get_class($this->value)))
        {
            return $this->construct_string_return('<div class="sub-struct">' . $this->value . '</div>');
        }

        if (is_object($this->value) && (ArrayStructureElement::class === get_class($this->value)))
        {
            return $this->construct_string_return('<div class="array-struct">' . $this->value . '</div>');
        }

        if (is_array($this->value) && (EnumStructureElement::class === get_class($this->value)))
        {
            return $this->construct_string_return('<div class="enum-struct">' . $this->value . '</div>');
        }

        $value = '<span class="example-value pull-right">';
        if (is_bool($this->value))
        {
            $value .= ($this->value) ? 'true' : 'false';
        } else {
            $value .= $this->value;
        }

        $value .= '</span>';

        return $this->construct_string_return($value);

    }

    /**
     * Create an HTML return
     *
     * @param string $value value to display
     *
     * @return string
     */
    function construct_string_return($value)
    {
        if (!in_array($this->type, self::DEFAULTS))
        {
            $type = '<a class="code" href="#object-' . str_replace(' ', '-',
                    strtolower($this->type)) . '">' . $this->type . '</a>';
        }
        else{
            if ($this->type === 'array')
            {
                $type = '<code>[ ' . join(',', $this->value->value) . ' ]</code>';
            }else {
                $type = '<code>' . $this->type . '</code>';
            }
        }

        $return =
            '<tr>' .
            '<td>' . '<span>' . $this->key . "</span>" . '</td>' .
            '<td>' . $type . '</td>' .
            '<td> <span class="status">' . $this->status . '</span></td>' .
            '<td>' . $this->description . '</td>' .
            '<td>' . $value . '</td>' .
            '</tr>';

        return $return;
    }

    /**
     * @return mixed|ObjectStructureElement|ObjectStructureElement[]
     */
    function strval()
    {
        if (is_array($this->value))
        {
            $key = rand(0, count($this->value));
            if (is_subclass_of($this->value[$key], StructureElement::class))
            {
                return $this->value[$key]->strval();
            }

            return $this->value[$key];
        }

        return $this->value;
    }

}