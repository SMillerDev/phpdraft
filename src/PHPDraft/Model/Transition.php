<?php

declare(strict_types=1);

/**
 * This file contains the Transition.
 *
 * @package PHPDraft\Model
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

use PHPDraft\Model\Elements\BasicStructureElement;
use PHPDraft\Model\Elements\ObjectStructureElement;
use PHPDraft\Model\Elements\StructureElement;
use QL\UriTemplate\Exception as UrlException;
use QL\UriTemplate\UriTemplate;

class Transition extends HierarchyElement
{
    /**
     * HTTP method used.
     *
     * @var string
     */
    public string $method;

    /**
     * URI.
     *
     * @var string|null
     */
    public ?string $href = null;

    /**
     * URL variables.
     *
     * @var StructureElement[]
     */
    public array $url_variables = [];

    /**
     * Data variables.
     *
     * @var StructureElement|null
     */
    public ?StructureElement $data_variables = null;

    /**
     * The request.
     *
     * @var HTTPRequest[]
     */
    public array $requests = [];

    /**
     * The responses.
     *
     * @var HTTPResponse[]
     */
    public array $responses = [];

    /**
     * Structures used (if any).
     *
     * @var StructureElement[]
     */
    public array $structures = [];

    /**
     * Transition constructor.
     *
     * @param Resource $parent A reference to the parent object
     */
    public function __construct(protected Resource $parent)
    {
        $this->parent = &$parent;
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
        parent::parse($object);

        $href = (isset($object->attributes->href)) ? $object->attributes->href : $this->parent->href;
        $this->href = $href->content ?? $href;

        if (isset($object->attributes->hrefVariables)) {
            $deps                = [];
            foreach ($object->attributes->hrefVariables->content as $variable) {
                $struct                = (new ObjectStructureElement())->get_class($variable->element);
                $this->url_variables[] = $struct->parse($variable, $deps);
            }
        }

        if (isset($object->attributes->data) && property_exists($object, 'element')) {
            $deps                 = [];
            $struct               = (new ObjectStructureElement())->get_class($object->element);
            $this->data_variables = $struct->parse($object->attributes->data->content, $deps);
        }

        if (!is_array($object->content)) {
            return $this;
        }
        foreach ($object->content as $transition_item) {
            if (!isset($transition_item->content)) {
                continue;
            }

            foreach ($transition_item->content as $item) {
                $parsable = null;
                switch ($item->element) {
                    case 'httpRequest':
                        $parsable = new HTTPRequest($this);
                        $val = $parsable->parse($item);
                        foreach ($this->requests as $request) {
                            if ($request->is_equal_to($val)) {
                                continue 3;
                            }
                        }
                        $this->requests[] = $val;
                        break;
                    case 'httpResponse':
                        $parsable = new HTTPResponse($this);
                        $val = $parsable->parse($item);
                        foreach ($this->responses as $response) {
                            if ($response->is_equal_to($val)) {
                                continue 3;
                            }
                        }
                        $this->responses[] = $parsable->parse($item);
                        break;
                    default:
                        continue 2;
                }
            }
        }

        return $this;
    }

    /**
     * Build a URL based on the URL variables given.
     *
     * @param string $base_url the URL to which the URL variables apply
     * @param bool   $clean    Get the URL without HTML
     *
     * @return string HTML representation of the transition URL
     *@throws UrlException
     *
     */
    public function build_url(string $base_url = '', bool $clean = false): string
    {
        $url = $this->overlap_urls($this->parent->href ?? '', $this->href);
        if ($url === NULL) {
            $url = $this->parent->href . $this->href;
        }
        $tpl  = new UriTemplate($url);
        $vars = [];
        if ($this->url_variables !== []) {
            foreach ($this->url_variables as $item) {
                if (!is_subclass_of($item, BasicStructureElement::class)) {
                    continue;
                }

                $vars[$item->key->value] = $item->string_value(true);
            }
        }
        if ($this->parent->url_variables !== []) {
            foreach ($this->parent->url_variables as $item) {
                if (!is_subclass_of($item, BasicStructureElement::class)) {
                    continue;
                }

                $vars[$item->key->value] = $item->string_value(true);
            }
        }
        $url = $tpl->expand($vars);

        if ($clean) {
            return strip_tags($base_url . $url);
        }

        return $base_url . $url;
    }

    /**
     * Overlap the URLS to get one consistent URL.
     *
     * @param string $str1 First part
     * @param string $str2 Second part
     *
     * @return null|string
     *
     * @see http://stackoverflow.com/questions/2945446/built-in-function-to-combine-overlapping-string-sequences-in-php
     */
    private function overlap_urls(string $str1, string $str2): ?string
    {
        $overlap = $this->find_overlap($str1, $str2);
        if ($overlap === NULL) {
            return NULL;
        }

        $overlap = $overlap[count($overlap) - 1];
        $str1    = substr($str1, 0, -strlen($overlap));
        $str2    = substr($str2, strlen($overlap));

        return $str1 . $overlap . $str2;
    }

    /**
     * Find overlap in strings.
     *
     * @param string $str1 First part
     * @param string $str2 Second part
     *
     * @return array<string>|null
     */
    private function find_overlap(string $str1, string $str2): ?array
    {
        $return = [];
        $sl1    = strlen($str1);
        $sl2    = strlen($str2);
        $max    = min($sl1, $sl2);
        $i      = 1;
        while ($i <= $max) {
            $s1 = substr($str1, -$i);
            $s2 = substr($str2, 0, $i);
            if ($s1 == $s2) {
                $return[] = $s1;
            }
            $i++;
        }
        if (!empty($return)) {
            return $return;
        }

        return NULL;
    }

    /**
     * Get the HTTP method of the child request.
     *
     * @param int $request Request to get the method for
     *
     * @return string HTTP Method
     */
    public function get_method(int $request = 0): string
    {
        return $this->requests[$request]->method ?? 'NONE';
    }

    /**
     * Generate a cURL request to run the transition.
     *
     * @param string        $base_url base URL of the server
     * @param array<string> $additional additional arguments to pass
     * @param int           $key number of the request to generate for
     *
     * @return string A cURL CLI command
     *
     * @throws UrlException If URL parts are invalid
     */
    public function get_curl_command(string $base_url, array $additional = [], int $key = 0): string
    {
        if (!isset($this->requests[$key])) {
            return '';
        }

        return $this->requests[$key]->get_curl_command($base_url, $additional);
    }
}
