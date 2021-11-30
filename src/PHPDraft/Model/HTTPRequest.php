<?php

declare(strict_types=1);

/**
 * This file contains the HTTPRequest.php.
 *
 * @package PHPDraft\Model
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

use PHPDraft\Model\Elements\RequestBodyElement;
use PHPDraft\Model\Elements\StructureElement;
use QL\UriTemplate\Exception;
use stdClass;

class HTTPRequest implements Comparable
{
    /**
     * HTTP Headers.
     *
     * @var array
     */
    public $headers = [];

    /**
     * The HTTP Method.
     *
     * @var string
     */
    public $method;

    /**
     * Title of the request.
     *
     * @var string
     */
    public $title;

    /**
     * Description of the request.
     *
     * @var string
     */
    public $description;

    /**
     * Parent class.
     *
     * @var Transition
     */
    public $parent;

    /**
     * Body of the request.
     *
     * @var mixed
     */
    public $body = null;

    /**
     * Schema of the body of the request.
     *
     * @var mixed
     */
    public $body_schema = null;
    /**
     * Structure of the request.
     *
     * @var RequestBodyElement[]|RequestBodyElement
     */
    public $struct = [];
    /**
     * Identifier for the request.
     *
     * @var string
     */
    protected $id;

    /**
     * HTTPRequest constructor.
     *
     * @param Transition $parent Parent entity
     */
    public function __construct(Transition &$parent)
    {
        $this->parent = &$parent;
        $this->id     = defined('ID_STATIC') ? ID_STATIC : md5(microtime());
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
        $this->method = $object->attributes->method->content ?? $object->attributes->method;
        $this->title  = $object->meta->title->content ?? $object->meta->title ?? null;

        if (isset($object->content) && $object->content !== null) {
            foreach ($object->content as $value) {
                if ($value->element === 'dataStructure') {
                    $this->parse_structure($value);
                    continue;
                }

                if ($value->element === 'copy') {
                    $this->description = $value->content;
                    continue;
                }

                if ($value->element !== 'asset') {
                    continue;
                }
                if (is_array($value->meta->classes) && in_array('messageBody', $value->meta->classes)) {
                    $this->body[]                  = (isset($value->content)) ? $value->content : null;
                    $this->headers['Content-Type'] = (isset($value->attributes->contentType)) ? $value->attributes->contentType : '';
                    continue;
                }

                if (
                    isset($value->meta->classes->content)
                    && is_array($value->meta->classes->content)
                    && $value->meta->classes->content[0]->content === 'messageBody'
                ) {
                    $this->body[]                  = (isset($value->content)) ? $value->content : null;
                    $this->headers['Content-Type'] = (isset($value->attributes->contentType->content)) ? $value->attributes->contentType->content : '';
                } elseif (
                    isset($value->meta->classes->content)
                    && is_array($value->meta->classes->content)
                    && $value->meta->classes->content[0]->content === 'messageBodySchema'
                ) {
                    $this->body_schema = (isset($value->content)) ? $value->content : null;
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
     * Parse the objects into a request body.
     *
     * @param stdClass $objects JSON objects
     */
    private function parse_structure(stdClass $objects): void
    {
        $deps      = [];
        $structure = new RequestBodyElement();
        $structure->parse($objects->content, $deps);
        $structure->deps = $deps;

        $this->struct = $structure;
    }

    public function get_id(): string
    {
        return $this->id;
    }

    /**
     * Generate a cURL command for the HTTP request.
     *
     * @param string $base_url   URL to the base server
     * @param array  $additional Extra options to pass to cURL
     *
     * @throws Exception
     *
     * @return string An executable cURL command
     */
    public function get_curl_command(string $base_url, array $additional = []): string
    {
        $options = [];

        $type = $this->headers['Content-Type'] ?? null;

        $options[] = '-X' . $this->method;
        if (is_null($this->body) || $this->body === []) {
            //NO-OP
        } elseif (is_string($this->body)) {
            $options[] = '--data-binary ' . escapeshellarg($this->body);
        } elseif (is_array($this->body)) {
            $options[] = '--data-binary ' . escapeshellarg(join('', $this->body));
        } elseif (is_subclass_of($this->struct, StructureElement::class)) {
            foreach ($this->struct->value as $body) {
                if (is_null($body) || $body === []) {
                    continue;
                }
                $options[] = '--data-binary ' . escapeshellarg(strip_tags($body->print_request($type)));
            }
        }
        foreach ($this->headers as $header => $value) {
            $options[] = '-H ' . escapeshellarg($header . ': ' . $value);
        }

        $options = array_merge($options, $additional);
        $url     = escapeshellarg($this->parent->build_url($base_url, true));

        return htmlspecialchars('curl ' . join(' ', $options) . ' ' . $url, ENT_NOQUOTES | ENT_SUBSTITUTE);
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
        if (!($b instanceof self)) {
            return false;
        }
        return ($this->method === $b->method)
            && ($this->body == $b->body)
            && ($this->headers == $b->headers)
            && ($this->title === $b->title);
    }

    /**
     * Convert class to string identifier
     */
    public function __toString()
    {
        $headers = json_encode($this->headers);
        $body = json_encode($this->body);
        return "{$this->method}_{$body}_{$headers}";
    }
}
