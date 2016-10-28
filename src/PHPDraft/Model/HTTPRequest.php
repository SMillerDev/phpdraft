<?php
/**
 * This file contains the HTTPRequest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

use PHPDraft\Model\Elements\RequestBodyElement;

class HTTPRequest
{
    /**
     * HTTP Headers
     *
     * @var array
     */
    public $headers = [];

    /**
     * The HTTP Method
     *
     * @var string
     */
    public $method;

    /**
     * Parent class
     *
     * @var Transition
     */
    public $parent;

    /**
     * Body of the request (if POST or PUT)
     *
     * @var mixed
     */
    public $body = null;


    /**
     * Structure of the request (if POST or PUT)
     *
     * @var RequestBodyElement[]
     */
    public $struct = [];

    /**
     * HTTPRequest constructor.
     *
     * @param Transition $parent Parent entity
     */
    public function __construct(&$parent)
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
        $this->method = $object->attributes->method;

        if (($this->method === 'POST' || $this->method === 'PUT') && !empty($object->content)) {
            foreach ($object->content as $value) {
                if ($value->element === 'dataStructure') {
                    $this->parse_structure($value);
                    continue;
                } elseif ($value->element === 'asset') {
                    if (in_array('messageBody', $value->meta->classes)) {
                        $this->body[]                  = (isset($value->content)) ? $value->content : null;
                        $this->headers['Content-Type'] =
                            (isset($value->attributes->contentType)) ? $value->attributes->contentType : '';
                    }
                }
            }
        }

        if (isset($object->attributes->headers)) {
            foreach ($object->attributes->headers->content as $value) {
                $this->headers[$value->content->key->content] = $value->content->value->content;
            }
        }

        if ($this->body === null) {
            $this->body = &$this->struct;
        }

        return $this;
    }

    /**
     * Parse the objects into a request body
     *
     * @param \stdClass $objects JSON objects
     */
    private function parse_structure($objects)
    {
        $deps   = [];
        $struct = new RequestBodyElement();
        $struct->parse($objects, $deps);
        $struct->deps = $deps;

        $this->struct = $struct;
    }

    /**
     * Generate a cURL command for the HTTP request
     *
     * @param string $base_url   URL to the base server
     *
     * @param array  $additional Extra options to pass to cURL
     *
     * @return string An executable cURL command
     */
    public function get_curl_command($base_url, $additional = [])
    {
        $options = [];

        $type = (isset($this->headers['Content-Type'])) ? $this->headers['Content-Type'] : null;

        $options[] = '-X' . $this->method;
        if (is_string($this->body)) {
            $options[] = '--data-binary "' . $this->body . '"';
        } elseif (is_array($this->body)) {
            $options[] = '--data-binary "' . join('', $this->body) . '"';
        } elseif (is_subclass_of($this->struct, StructureElement::class)) {
            foreach ($this->struct->value as $body) {
                $options[] = '--data-binary "' . strip_tags($body->print_request($type)) . '"';
            }
        }
        foreach ($this->headers as $header => $value) {
            $options[] = '-H "' . $header . ': ' . $value . '"';
        }
        $options = array_merge($options, $additional);

        return htmlspecialchars('curl ' . join(' ', $options) . ' "' . $this->parent->build_url($base_url, true) . '"');
    }


}