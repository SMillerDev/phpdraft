<?php
/**
 * This file contains the Transition
 *
 * @package PHPDraft\Model
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

use PHPDraft\Model\Elements\DataStructureElement;

class Transition extends HierarchyElement
{
    /**
     * HTTP method used
     *
     * @var string
     */
    public $method;

    /**
     * URI
     *
     * @var string
     */
    public $href;

    /**
     * URL variables
     *
     * @var DataStructureElement|NULL
     */
    public $url_variables = null;

    /**
     * Data variables
     *
     * @var array
     */
    public $data_variables = null;

    /**
     * The request
     *
     * @var HTTPRequest
     */
    public $request;

    /**
     * The responses
     *
     * @var HTTPResponse[]
     */
    public $responses;

    /**
     * Structures used (if any)
     *
     * @var DataStructureElement[]
     */
    public $structures = [];

    /**
     * Transition constructor.
     *
     * @param Resource $parent A reference to the parent object
     */
    public function __construct(&$parent)
    {
        $this->parent = $parent;
    }

    /**
     * Fill class values based on JSON object
     *
     * @param \stdClass $object JSON object
     *
     * @return $this self-reference
     */
    function parse($object)
    {
        parent::parse($object);

        $this->href = (isset($object->attributes->href)) ? $object->attributes->href : $this->parent->href;

        if (isset($object->attributes->hrefVariables)) {

            $deps                = [];
            $struct              = new DataStructureElement();
            $this->url_variables = $struct->parse($object->attributes->hrefVariables, $deps);
        }

        if (isset($object->attributes->data)) {
            $deps                 = [];
            $struct               = new DataStructureElement();
            $this->data_variables = $struct->parse($object->attributes->data, $deps);
        }

        if (isset($object->content[0]->content)) {
            foreach ($object->content[0]->content as $item) {
                if ($item->element === 'httpRequest') {
                    $this->request = new HTTPRequest($this);
                    $this->request->parse($item);
                } elseif ($item->element === 'httpResponse') {
                    $response          = new HTTPResponse($this);
                    $this->responses[] = $response->parse($item);
                }
            }
        }

        return $this;
    }

    /**
     * Build a URL based on the URL variables given
     *
     * @param string $base_url the URL to which the URL variables apply
     *
     * @param bool   $clean    Get the URL without HTML
     *
     * @return string a HTML representation of the transition URL
     */
    public function build_url($base_url = '', $clean = false)
    {
        $url = $this->overlap_urls($this->parent->href, $this->href);
        if ($url === false) {
            $url = $this->parent->href . $this->href;
        }
        if ($this->url_variables !== null) {
            foreach ($this->url_variables->value as $value) {
                $urlvalue = $value->value;
                if (is_subclass_of($value, StructureElement::class)) {
                    $urlvalue = $value->strval();
                }

                $url =
                    preg_replace('/({\?' . $value->key . '})/',
                        '?<var class="url-param">' . $value->key . '</var>=<var class="url-value">' . urlencode($urlvalue) . '</var>',
                        $url);
                $url =
                    preg_replace('/({\&' . $value->key . '})/',
                        '&<var class="url-param">' . $value->key . '</var>=<var class="url-value">' . urlencode($urlvalue) . '</var>',
                        $url);
                $url =
                    preg_replace('/({' . $value->key . '})/',
                        '<var class="url-value">' . urlencode($urlvalue) . '</var>', $url);
            }
        }

        if ($clean) {
            return strip_tags($base_url . $url);
        }

        return $base_url . $url;
    }

    /**
     * Overlap the URLS to get one consistent URL
     *
     * @param $str1
     * @param $str2
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

        return false;
    }

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

        return false;
    }

    /**
     * Get the HTTP method of the child request
     *
     * @return string HTTP Method
     */
    public function get_method()
    {
        return (isset($this->request->method)) ? $this->request->method : 'NONE';
    }

    /**
     * Generate a cURL request to run the transition
     *
     * @param string $base_url   base URL of the server
     *
     * @param array  $additional additional arguments to pass
     *
     * @return string A cURL CLI command
     */
    public function get_curl_command($base_url, $additional = [])
    {
        return $this->request->get_curl_command($base_url, $additional);
    }

}