<?php
/**
 * This file contains the HTTPRequest.php
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
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

    /**
     * Body of the request (if POST or PUT)
     * @var RequestBodyElement[]
     */
    public $body;

    public function __construct(&$parent)
    {
        $this->parent = &$parent;
        //TODO: Parse body
    }

    public function parse($object)
    {
        $this->method = $object->attributes->method;

        if (($this->method === 'POST' || $this->method === 'PUT') && !empty($object->content))
        {
            foreach ($object->content as $value)
            {
                if ($value->element === 'dataStructure')
                {
                    $this->parse_structure($value->content);
                    continue;
                }
            }
        }

        if (isset($object->attributes->headers))
        {
            foreach ($object->attributes->headers->content as $value)
            {
                $this->headers[$value->content->key->content] = $value->content->value->content;
            }
        }

        return $this;
    }

    private function parse_structure($objects)
    {
        foreach ($objects as $object)
        {
            $deps   = [];
            $struct = new RequestBodyElement();
            $struct->parse($object, $deps);
            $struct->deps = $deps;

            $this->body[] = $struct;
        }
    }

    public function get_curl_command($base_url)
    {
        $options = [];

        $type = (isset($this->headers['Content-Type']))?$this->headers['Content-Type']:NULL;

        $options[] = '-X'.$this->method;
        foreach ($this->body as $body)
        {
            $options[] = '--data-binary "'.strip_tags($body->print_request($type)).'"';
        }
        foreach ($this->headers as $header=>$value)
        {
            $options[] = '-H "'.$header.': '.$value. '"';
        }
        $options[] = '-v';
        return htmlspecialchars('curl '.join(' ', $options). ' "'.$base_url.$this->parent->build_url().'"');
    }


}