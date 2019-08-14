<?php
/**
 * This file contains the HTTPRequest.php.
 *
 * @package PHPDraft\Model
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

use Michelf\MarkdownExtra;
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
     * Body of the request (if POST or PUT).
     *
     * @var mixed
     */
    public $body = NULL;

    /**
     * Identifier for the request.
     *
     * @var string
     */
    protected $id;

    /**
     * Structure of the request (if POST or PUT).
     *
     * @var RequestBodyElement
     */
    public $struct = [];

    /**
     * HTTPRequest constructor.
     *
     * @param Transition $parent Parent entity
     */
    public function __construct(Transition &$parent)
    {
        $this->parent = &$parent;
        $this->id     = md5(microtime());
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
        $this->method = $object->attributes->method->content ?? $object->attributes->method;
        $this->title  = isset($object->meta->title) ? $object->meta->title : NULL;

        if (($this->method === 'POST' || $this->method === 'PUT') && !empty($object->content)) {
            foreach ($object->content as $value) {
                if ($value->element === 'dataStructure') {
                    $this->parse_structure($value);
                    continue;
                } elseif ($value->element === 'copy') {
                    $this->description = MarkdownExtra::defaultTransform(htmlentities($value->content));
                } elseif ($value->element === 'asset') {
                    if (in_array('messageBody', $value->meta->classes)) {
                        $this->body[]                  = (isset($value->content)) ? $value->content : NULL;
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

        if ($this->body === NULL) {
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
        $deps   = [];
        $struct = new RequestBodyElement();
        $struct->parse($objects, $deps);
        $struct->deps = $deps;

        $this->struct = $struct;
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

        $type = $this->headers['Content-Type'] ?? NULL;

        $options[] = '-X' . $this->method;
        if (empty($this->body)) {
            //NO-OP
        } elseif (is_string($this->body)) {
            $options[] = '--data-binary ' . escapeshellarg($this->body);
        } elseif (is_array($this->body)) {
            $options[] = '--data-binary ' . escapeshellarg(join('', $this->body));
        } elseif (is_subclass_of($this->struct, StructureElement::class)) {
            foreach ($this->struct->value as $body) {
                $options[] = '--data-binary ' . escapeshellarg(strip_tags($body->print_request($type)));
            }
        }
        foreach ($this->headers as $header => $value) {
            $options[] = '-H ' . escapeshellarg($header . ': ' . $value);
        }
        $options = array_merge($options, $additional);

        return htmlspecialchars('curl ' . join(' ', $options) . ' ' . escapeshellarg($this->parent->build_url($base_url, TRUE)));
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
        return ($this->method === $b->method) && ($this->body === $b->body) && ($this->headers === $b->headers);
    }

    /**
     * Generate a URL for the hurl.it service.
     *
     * @param string $base_url   URL to the base server
     * @param array  $additional Extra options to pass to the service
     *
     * @throws Exception
     *
     * @return string
     */
    public function get_hurl_link(string $base_url, array $additional = []): string
    {
        $options = [];

        $type = (isset($this->headers['Content-Type'])) ? $this->headers['Content-Type'] : NULL;

        $url = $this->parent->build_url($base_url, TRUE);
        $url = explode('?', $url);
        if (isset($url[1])) {
            $params = [];
            foreach (explode('&', $url[1]) as $args) {
                $arg             = explode('=', $args);
                $params[$arg[0]] = [$arg[1]];
            }
            $options[] = 'args=' . urlencode(json_encode($params));
        }
        $options[] = 'url=' . urlencode($url[0]);
        $options[] = 'method=' . strtoupper($this->method);
        if (empty($this->body)) {
            //NO-OP
        } elseif (is_string($this->body)) {
            $options[] = 'body=' . urlencode($this->body);
        } elseif (is_array($this->body)) {
            $options[] = 'body=' . urlencode(join(',', $this->body));
        } elseif (is_subclass_of($this->struct, StructureElement::class)) {
            foreach ($this->struct->value as $body) {
                $options[] = 'body=' . urlencode(strip_tags($body->print_request($type)));
            }
        }
        $headers = [];
        if (!empty($this->headers)) {
            foreach ($this->headers as $header => $value) {
                $headers[$header] = [$value];
            }
            $options[] = 'headers=' . urlencode(json_encode($headers));
        }
        $options = array_merge($options, $additional);

        return 'https://www.hurl.it/?' . join('&', $options);
    }
}
