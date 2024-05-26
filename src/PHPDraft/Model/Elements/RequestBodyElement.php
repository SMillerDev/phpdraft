<?php

declare(strict_types=1);

/**
 * This file contains the RequestBodyElement.
 *
 * @package PHPDraft\Model\Elements
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements;

/**
 * Class RequestBodyElement.
 */
class RequestBodyElement extends ObjectStructureElement
{
    /**
     * Print the request body as a string.
     *
     * @param string|null $type The type of request
     *
     * @return string Request body
     */
    public function print_request(?string $type = 'application/x-www-form-urlencoded'): string
    {
        if (is_array($this->value)) {
            $return = '<code class="request-body">';
            $list   = [];
            foreach ($this->value as $object) {
                if (get_class($object) === self::class) {
                    $list[] = $object->print_request($type);
                }
            }

            $return .= match ($type) {
                'application/x-www-form-urlencoded' => join('&', $list),
                default => join(PHP_EOL, $list),
            };

            $return .= '</code>';

            return $return;
        }

        $value = ($this->value === null || $this->value === '') ? '?' : $this->value;

        switch ($type) {
            case 'application/x-www-form-urlencoded':
                return "{$this->key->value}=<span>$value</span>";
            default:
                $object             = [];
                $object[$this->key->value] = $value;

                $encoded = json_encode($object);
                return is_string($encoded) ? $encoded : '';
        }
    }

    /**
     * Return a new instance.
     *
     * @return RequestBodyElement
     */
    protected function new_instance(): self
    {
        return new self();
    }
}
