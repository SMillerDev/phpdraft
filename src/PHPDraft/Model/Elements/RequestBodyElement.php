<?php
/**
 * This file contains the RequestBodyElement
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements;

use PHPDraft\Model\StructureElement;

class RequestBodyElement extends DataStructureElement implements StructureElement
{
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
                $struct        = new RequestBodyElement();
                $this->value[] = $struct->parse($value, $dependencies);
            }

            return $this;
        }

        $this->key         = $object->content->key->content;
        $this->type        = $object->content->value->element;
        $this->description = isset($object->meta->description) ? htmlentities($object->meta->description) : null;
        $this->description_as_html();
        $this->status =
            isset($object->attributes->typeAttributes[0]) ? $object->attributes->typeAttributes[0] : null;

        if (!in_array($this->type, parent::DEFAULTS)) {
            $dependencies[] = $this->type;
        }

        if ($this->type === 'object') {
            $value       = isset($object->content->value->content) ? $object->content->value : null;
            $this->value = new RequestBodyElement();
            $this->value = $this->value->parse($value, $dependencies);

            return $this;
        }

        if ($this->type === 'array') {
            $this->value = '[]';

            return $this;
        }

        $this->value = isset($object->content->value->content) ? $object->content->value->content : null;

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
        if (is_array($this->value)) {
            $return = '<code class="request-body">';
            $list   = [];
            foreach ($this->value as $object) {
                if (get_class($object) === self::class) {
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
                return $this->key . "=<span>" . $value . '</span>';
                break;
            default:
                $object             = [];
                $object[$this->key] = $value;

                return json_encode($object);
                break;
        }
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