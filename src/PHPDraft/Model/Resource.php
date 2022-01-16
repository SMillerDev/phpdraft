<?php

declare(strict_types=1);

/**
 * This file contains the Resource.php.
 *
 * @package PHPDraft\Model
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

use PHPDraft\Model\Elements\ObjectStructureElement;

class Resource extends HierarchyElement
{
    /**
     * Location relative to the base URL.
     *
     * @var string|null
     */
    public ?string $href = null;

    /**
     * URL variables.
     *
     * @var ObjectStructureElement[]
     */
    public array $url_variables = [];

    /**
     * Resource constructor.
     *
     * @param Category $parent A reference to the parent object
     */
    public function __construct(Category &$parent)
    {
        $this->parent = $parent;
    }

    /**
     * Fill class values based on JSON object.
     *
     * @param object $object JSON object
     *
     * @return $this self-reference
     */
    public function parse(object $object): self
    {
        parent::parse($object);

        if (isset($object->attributes)) {
            $this->href = $object->attributes->href->content ?? $object->attributes->href;
        }

        if (isset($object->attributes->hrefVariables)) {
            $deps                = [];
            foreach ($object->attributes->hrefVariables->content as $variable) {
                $struct                = new ObjectStructureElement();
                $this->url_variables[] = $struct->parse($variable, $deps);
            }
        }

        foreach ($object->content as $item) {
            $transition       = new Transition($this);
            $this->children[] = $transition->parse($item);
        }

        return $this;
    }
}
