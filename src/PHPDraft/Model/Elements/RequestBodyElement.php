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
                if (get_class($object) !== self::class) {
                    continue;
                }
                $list[] = $object->print_request($type);
            }

            $return .= match ($type)
            {
                'application/x-www-form-urlencoded' => join('&', $list),
                default => join(PHP_EOL, $list),
            };

            $return .= '</code>';

            return $return;
        }

        $value = $this->value ?? '?';

        return match ($type) {
            'application/x-www-form-urlencoded' => "{$this->key->value}=<span>$value</span>",
            default => json_encode([$this->key->value => $value]),
        };
    }

    /**
     * Return a new instance.
     *
     * @return RequestBodyElement
     */
    protected function new_instance(): StructureElement
    {
        return new self();
    }
}
