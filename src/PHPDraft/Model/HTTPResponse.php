<?php

declare(strict_types=1);

/**
 * This file contains the HTTPResponse.php.
 *
 * @package PHPDraft\Model
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

use PHPDraft\Model\Elements\ObjectStructureElement;

class HTTPResponse implements Comparable
{
    /**
     * HTTP Status code.
     *
     * @var int
     */
    public int $statuscode;

    /**
     * Description of the object.
     *
     * @var string|null
     */
    public ?string $description = null;

    /**
     * Identifier for the request.
     *
     * @var string
     */
    protected string $id;

    /**
     * Response headers.
     *
     * @var array<int|string, string>
     */
    public array $headers = [];

    /**
     * Response bodies.
     *
     * @var array<int|string, string>
     */
    public array $content = [];

    /**
     * Response structure.
     *
     * @var ObjectStructureElement[]
     */
    public array $structure = [];

    /**
     * Parent entity.
     *
     * @var Transition
     */
    protected Transition $parent;

    public function __construct(Transition $parent)
    {
        $this->parent = &$parent;
        $this->id     = defined('ID_STATIC') ? ID_STATIC : md5(microtime());
    }

    /**
     * Fill class values based on JSON object.
     *
     * @param object $object JSON object
     *
     * @return self self-reference
     */
    public function parse(object $object): self
    {
        if (isset($object->attributes->statusCode->content)) {
            $this->statuscode = intval($object->attributes->statusCode->content);
        } elseif (isset($object->attributes->statusCode)) {
            $this->statuscode = intval($object->attributes->statusCode);
        }
        if (isset($object->attributes->headers)) {
            $this->parse_headers($object->attributes->headers);
        }

        foreach ($object->content as $value) {
            $this->parse_content($value);
        }

        return $this;
    }

    public function get_id(): string
    {
        return $this->id;
    }

    /**
     * Parse request headers.
     *
     * @param object $object An object to parse for headers
     *
     * @return void
     */
    protected function parse_headers(object $object): void
    {
        foreach ($object->content as $value) {
            if (isset($value->content)) {
                $this->headers[$value->content->key->content] = $value->content->value->content;
            }
        }
    }

    /**
     * Parse request content.
     *
     * @param object $value An object to parse for content
     *
     * @return void
     */
    protected function parse_content(object $value): void
    {
        if ($value->element === 'copy') {
            $this->description = $value->content;
            return;
        }

        if ($value->element === 'asset') {
            if (isset($value->attributes->contentType->content)) {
                $this->content[$value->attributes->contentType->content] = $value->content;
            } elseif (isset($value->attributes->contentType)) {
                $this->content[$value->attributes->contentType] = $value->content;
            }
            return;
        }

        if ($value->element === 'dataStructure') {
            foreach ($value->content->content as $object) {
                $this->parse_structure($object);
            }
        }
    }

    /**
     * Parse structure of the content.
     *
     * @param object $object Objects containing the structure
     *
     * @return void
     */
    protected function parse_structure(object $object): void
    {
        $deps   = [];
        $struct = new ObjectStructureElement();
        $struct->parse($object, $deps);
        $struct->deps = $deps;
        foreach ($this->structure as $prev) {
            if ($struct->__toString() === $prev->__toString()) {
                return;
            }
        }

        $this->structure[] = $struct;
    }

    /**
     * Check if item is the same as other item.
     *
     * @param object $b Object to compare to
     *
     * @return bool
     */
    public function is_equal_to(object $b): bool
    {
        if (!($b instanceof self)) {
            return false;
        }
        return ($this->statuscode === $b->statuscode)
            && ($this->description === $b->description);
    }

    /**
     * Convert class to string identifier
     */
    public function __toString(): string
    {
        return "{$this->statuscode}_{$this->description}";
    }
}
