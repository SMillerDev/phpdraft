<?php
/**
 * This file contains the RequestBodyElement
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements;

use PHPDraft\Model\StructureElement;

/**
 * Class RequestBodyElement
 */
class RequestBodyElement extends ObjectStructureElement implements StructureElement
{

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
        if ($this->type === 'object')
        {
            $value       = isset($object->content->value->content) ? $object->content->value : NULL;
            $this->value = new RequestBodyElement();
            $this->value = $this->value->parse($value, $dependencies);

            return $this;
        }
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
     * Print the request body as a string
     *
     * @param string $type The type of request
     *
     * @return string Request body
     */
    public function print_request($type = 'application/x-www-form-urlencoded')
    {
        if (is_array($this->value))
        {
            $return = '<code class="request-body">';
            $list   = [];
            foreach ($this->value as $object) {
                if (get_class($object) === self::class)
                {
                    $list[] = $object->print_request($type);
                }
            }

            switch ($type) {
                case 'application/x-www-form-urlencoded':
                    $return .= join('&', $list);
                    break;
                default:
                    $return .= join(PHP_EOL, $list);
                    break;
            }

            $return .= '</code>';

            return $return;
        }

        $value = (empty($this->value)) ? '?' : $this->value;

        switch ($type) {
            case 'application/x-www-form-urlencoded':
                return $this->key . '=<span>' . $value . '</span>';
                break;
            default:
                $object             = [];
                $object[$this->key] = $value;

                return json_encode($object);
                break;
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
            case 'enum':
                $struct      = new EnumStructureElement();
                $this->value = $struct->parse($object, $dependencies);
                break;
            case 'object':
            default:
                $value  = isset($object->content->value->content) ? $object->content->value->content : NULL;
                $struct = new RequestBodyElement();

                $this->value = $struct->parse($value, $dependencies);
                break;
        }

        unset($struct);
        unset($value);
    }

    /**
     *
     * @return string
     */
    function __toString()
    {
        return parent::__toString();
    }

}