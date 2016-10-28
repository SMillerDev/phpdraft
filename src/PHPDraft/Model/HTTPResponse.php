<?php
/**
 * This file contains the HTTPResponse.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

use PHPDraft\Model\Elements\DataStructureElement;

class HTTPResponse
{
    /**
     * HTTP Status code
     *
     * @var int
     */
    public $statuscode;

    /**
     * Response headers
     *
     * @var array
     */
    public $headers = [];

    /**
     * Response bodies
     *
     * @var array
     */
    public $content = [];

    /**
     * Response structure
     *
     * @var DataStructureElement[]
     */
    public $structure = [];

    /**
     * Parent entity
     *
     * @var Transition
     */
    protected $parent;

    public function __construct($parent)
    {
        $this->parent = &$parent;
    }

    /**
     * Fill class values based on JSON object
     *
     * @param \stdClass $object JSON object
     *
     * @return $this self-reference
     */
    public function parse($object)
    {
        $this->statuscode = intval($object->attributes->statusCode);

        if (isset($object->attributes)) {
            $this->parse_headers($object->attributes->headers);
        }

        $this->parse_content($object);

        return $this;
    }

    /**
     * Parse request headers
     *
     * @param \stdClass $object An object to parse for headers
     *
     * @return void
     */
    protected function parse_headers($object)
    {
        foreach ($object->content as $value) {
            if (isset($value->content)) {
                $this->headers[$value->content->key->content] = $value->content->value->content;
            }
        }
    }

    /**
     * Parse request content
     *
     * @param \stdClass $object An object to parse for content
     *
     * @return void
     */
    protected function parse_content($object)
    {
        foreach ($object->content as $value) {
            if ($value->element === 'dataStructure') {
                $this->parse_structure($value->content);
                continue;
            }

            if (isset($value->attributes)) {
                $this->content[$value->attributes->contentType] = $value->content;
            }
        }
    }

    /**
     * Parse structure of the content
     *
     * @param \stdClass[] $objects Objects containing the structure
     *
     * @return void
     */
    protected function parse_structure($objects)
    {
        foreach ($objects as $object) {
            $deps   = [];
            $struct = new DataStructureElement();
            $struct->parse($object, $deps);
            $struct->deps = $deps;

            $this->structure[] = $struct;
        }
    }
}