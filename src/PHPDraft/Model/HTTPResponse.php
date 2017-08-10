<?php
/**
 * This file contains the HTTPResponse.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

use Michelf\MarkdownExtra;
use PHPDraft\Model\Elements\ObjectStructureElement;

class HTTPResponse implements Comparable
{
    /**
     * HTTP Status code
     *
     * @var int
     */
    public $statuscode;

    /**
     * Description of the object
     *
     * @var string
     */
    public $description;

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
     * @var ObjectStructureElement[]
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
        if (isset($object->attributes->statusCode)) {
            $this->statuscode = intval($object->attributes->statusCode);
        }
        if (isset($object->attributes->headers)) {
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
            }else if ($value->element === 'copy'){
                $this->description = MarkdownExtra::defaultTransform(htmlentities($value->content));
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
            $struct = new ObjectStructureElement();
            $struct->parse($object, $deps);
            $struct->deps = $deps;

            $this->structure[] = $struct;
        }
    }

    /**
     * Check if item is the same as other item
     *
     * @param self $b Object to compare to
     *
     * @return bool
     */
    public function is_equal_to($b)
    {
        return (($this->statuscode === $b->statuscode) && ($this->description === $b->description));
    }
}