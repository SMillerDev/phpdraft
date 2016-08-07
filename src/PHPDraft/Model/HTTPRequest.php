<?php
/**
 * This file contains the HTTPRequest.php
 *
 * @package PHPDraft\Model
 * @author Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

class HTTPRequest
{
    /**
     * HTTP Headers
     * @var array
     */
    public $headers = [];

    /**
     * The HTTP Method
     * @var string
     */
    public $method;

    /**
     * Parent class
     * @var Transition
     */
    public $parent;

    public function __construct(&$parent)
    {
        $this->parent = &$parent;
        //TODO: Parse body
    }

    public function parse($object)
    {
        $this->method = $object->attributes->method;
        if (isset($object->attributes->headers))
        {
            foreach ($object->attributes->headers->content as $value)
            {
                $this->headers[$value->content->key->content] = $value->content->value->content;
            }
        }

        return $this;
    }
}