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
use stdClass;

class HTTPResponse implements Comparable
{
    /**
     * HTTP Status code.
     *
     * @var int
     */
    public $statuscode;

    /**
     * Description of the object.
     *
     * @var string
     */
    public $description;

    /**
     * Identifier for the request.
     *
     * @var string
     */
    protected $id;

    /**
     * Response headers.
     *
     * @var array
     */
    public $headers = [];

    /**
     * Response bodies.
     *
     * @var array
     */
    public $content = [];

    /**
     * Response structure.
     *
     * @var ObjectStructureElement[]
     */
    public $structure = [];

    /**
     * Parent entity.
     *
     * @var Transition
     */
    protected $parent;

    public function __construct(Transition $parent)
    {
        $this->parent = &$parent;
        $this->id     = defined('ID_STATIC') ? ID_STATIC : md5(microtime());
    }

    /**
     * Fill class values based on JSON object.
     *
     * @param stdClass $object JSON object
     *
     * @return $this self-reference
     */
    public function parse(stdClass $object): self
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
     * @param stdClass $object An object to parse for headers
     *
     * @return void
     */
    protected function parse_headers(stdClass $object): void
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
     * @param stdClass $value An object to parse for content
     *
     * @return void
     */
    protected function parse_content(stdClass $value): void
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
            return;
        }
    }

    /**
     * Parse structure of the content.
     *
     * @param stdClass $object Objects containing the structure
     *
     * @return void
     */
    protected function parse_structure(stdClass $object): void
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
     * @param self $b Object to compare to
     *
     * @return bool
     */
    public function is_equal_to($b): bool
    {
        if (!($b instanceof self)){
            return false;
        }
        return ($this->statuscode === $b->statuscode)
            && ($this->description === $b->description);
    }

    /**
     * Convert class to string identifier
     */
    public function __toString()
    {
        return "{$this->statuscode}_{$this->description}";
    }
}
