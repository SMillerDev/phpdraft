<?php

declare(strict_types=1);

/**
 * This file contains the ${FILE_NAME}.
 *
 * @package PHPDraft\Model
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements;

use Michelf\MarkdownExtra;

class EnumStructureElement extends BasicStructureElement
{
    /**
     * Parse an array object.
     *
     * @param object|null $object       APIB Item to parse
     * @param string[]    $dependencies List of dependencies build
     *
     * @return self
     */
    public function parse(?object $object, array &$dependencies): self
    {
        $this->element = $object->element;

        $this->parse_common($object, $dependencies);
        if (!isset($this->key) && isset($object->content->content)) {
            $this->key = new ElementStructureElement();
            $this->key->parse($object->content, $dependencies);
        }
        $this->type  = $this->type ?? $object->content->element ?? null;

        if (!isset($object->content) && !isset($object->attributes)) {
            $this->value = $this->key;

            return $this;
        }

        if (isset($object->attributes->default)) {
            if (!in_array($object->attributes->default->content->element ?? '', self::DEFAULTS, true)) {
                $dependencies[] = $object->attributes->default->content->element;
            }
            $this->value = $object->attributes->default->content->content;
            $this->deps  = $dependencies;

            return $this;
        }

        if (isset($object->content)) {
            if (!in_array($object->content->element, self::DEFAULTS, true)) {
                $dependencies[] = $object->content->element;
            }
            $this->value = $object->content->content;
        }

        if (isset($object->attributes->enumerations->content) && $object->attributes->enumerations->content !== []) {
            $this->value = [];

            foreach ($object->attributes->enumerations->content as $sub_item) {
                $element = new ElementStructureElement();
                $element->parse($sub_item, $dependencies);
                $this->value[] = $element;
            }
        }

        $this->deps = $dependencies;

        return $this;
    }

    /**
     * Provide HTML representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        if (is_iterable($this->value)) {
            $return = '';
            foreach ($this->value as $item) {
                $return .= $item->__toString();
            }

            return '<ul class="list-group mdl-list">' . $return . '</ul>';
        }

        $type = $this->get_element_as_html($this->element);
        $desc = $this->description === null ? '' : MarkdownExtra::defaultTransform($this->description);

        return "<tr><td>{$this->key->value}</td><td>$type</td><td>$desc</td></tr>";
    }

    /**
     * Get a new instance of a class.
     *
     * @return self
     */
    protected function new_instance(): self
    {
        return new self();
    }
}
