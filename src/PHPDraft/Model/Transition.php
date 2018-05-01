<?php
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
use QL\UriTemplate\UriTemplate;

class Transition extends HierarchyElement
{
    /**
     * HTTP method used.
     *
     * @var string
     */
    public $method;

    /**
     * URI.
     *
     * @var string
     */
    public $href;

    /**
     * URL variables.
     *
     * @var ObjectStructureElement|null
     */
    public $url_variables = NULL;

    /**
     * Data variables.
     *
     * @var array
     */
    public $data_variables = NULL;

    /**
     * The request.
     *
     * @var HTTPRequest[]
     */
    public $requests = [];

    /**
     * The responses.
     *
     * @var HTTPResponse[]
     */
    public $responses = [];

    /**
     * Structures used (if any).
     *
     * @var ObjectStructureElement[]
     */
    public $structures = [];

    /**
     * Transition constructor.
     *
     * @param resource $parent A reference to the parent object
     */
    public function __construct(&$parent)
    {
        $this->parent = $parent;
    }

    /**
     * Fill class values based on JSON object.
     *
     * @param \stdClass $object JSON object
     *
     * @return $this self-reference
     */
    public function parse($object)
    {
        parent::parse($object);

        $this->href = (isset($object->attributes->href)) ? $object->attributes->href : $this->parent->href;

        if (isset($object->attributes->hrefVariables)) {
            $deps                = [];
            $struct              = new ObjectStructureElement();
            $this->url_variables = $struct->parse($object->attributes->hrefVariables, $deps);
        }

        if (isset($object->attributes->data)) {
            $deps                 = [];
            $struct               = new ObjectStructureElement();
            $this->data_variables = $struct->parse($object->attributes->data, $deps);
        }

        if (!is_array($object->content)) {
            return $this;
        }
        foreach ($object->content as $transition_item) {
            if (!isset($transition_item->content)) {
                continue;
            }
            foreach ($transition_item->content as $item) {
                $value = NULL;
                if (!in_array($item->element, ['httpRequest', 'httpResponse'])) {
                    continue;
                }
                switch ($item->element) {
                    case 'httpRequest':
                        $value = new HTTPRequest($this);
                        $list  = &$this->requests;
                        break;
                    case 'httpResponse':
                        $value = new HTTPResponse($this);
                        $list  = &$this->responses;
                        break;
                    default:
                        continue;
                        break;
                }
                $value->parse($item);

                if (empty($list)) {
                    $list[] = $value;
                    continue;
                }
                $add = TRUE;
                foreach ($list as $existing_value) {
                    if ($existing_value->is_equal_to($value)) {
                        $add = FALSE;
                    }
                }
                if ($add) {
                    $list[] = $value;
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
     * @return string a HTML representation of the transition URL
     * @throws \QL\UriTemplate\Exception
     */
    public function build_url($base_url = '', $clean = FALSE)
    {
        $url = $this->overlap_urls($this->parent->href, $this->href);
        if ($url === FALSE) {
            $url = $this->parent->href . $this->href;
        }
        if ($this->url_variables !== NULL) {
            $tpl  = new UriTemplate($url);
            $vars = [];
            foreach ($this->url_variables->value as $item) {
                $urlvalue = $item->value;
                if (is_subclass_of($item, BasicStructureElement::class)) {
                    $urlvalue = $item->string_value();
                }

                $vars[$item->key] = $urlvalue;
            }
            $url = $tpl->expand($vars);
        }

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
     * @return bool|string
     *
     * @see http://stackoverflow.com/questions/2945446/built-in-function-to-combine-overlapping-string-sequences-in-php
     */
    private function overlap_urls($str1, $str2)
    {
        if ($overlap = $this->find_overlap($str1, $str2)) {
            $overlap = $overlap[count($overlap) - 1];
            $str1    = substr($str1, 0, -strlen($overlap));
            $str2    = substr($str2, strlen($overlap));

            return $str1 . $overlap . $str2;
        }

        return FALSE;
    }

    /**
     * Find overlap in strings.
     *
     * @param string $str1 First part
     * @param string $str2 Second part
     *
     * @return array|bool
     */
    private function find_overlap($str1, $str2)
    {
        $return = [];
        $sl1    = strlen($str1);
        $sl2    = strlen($str2);
        $max    = $sl1 > $sl2 ? $sl2 : $sl1;
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

        return FALSE;
    }

    /**
     * Get the HTTP method of the child request.
     *
     * @param int $request Request to get the method for
     *
     * @return string HTTP Method
     */
    public function get_method($request = 0)
    {
        return (isset($this->requests[$request]->method)) ? $this->requests[$request]->method : 'NONE';
    }

    /**
     * Generate a cURL request to run the transition.
     *
     * @param string $base_url   base URL of the server
     * @param array  $additional additional arguments to pass
     * @param int    $key        number of the request to generate for
     *
     * @return string A cURL CLI command
     */
    public function get_curl_command($base_url, $additional = [], $key = 0)
    {
        if (!isset($this->requests[$key])) {
            return '';
        }

        return $this->requests[$key]->get_curl_command($base_url, $additional);
    }

    /**
     * Generate a URL for the hurl.it service.
     *
     * @param string $base_url   base URL of the server
     * @param array  $additional additional arguments to pass
     * @param int    $key        number of the request to generate for
     *
     * @return string
     */
    public function get_hurl_link($base_url, $additional = [], $key = 0)
    {
        if (!isset($this->requests[$key])) {
            return '';
        }

        return $this->requests[$key]->get_hurl_link($base_url, $additional);
    }
}
