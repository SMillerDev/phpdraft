<?php
/**
 * This file contains the Transition
 *
 * @package PHPDraft\Model
 * @author Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;

class Transition extends APIBlueprintElement
{
    /**
     * HTTP method used
     *
     * @var string
     */
    public $method;

    /**
     * URI
     * @var string
     */
    public $href;

    /**
     * URL variables
     * @var array
     */
    public $url_variables = [];

    /**
     * Data variables
     * @var array
     */
    public $data_variables = [];

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

    function parse($object)
    {
        parent::parse($object);

        $this->href = (isset($object->attributes->href)) ? $object->attributes->href : $this->parent->href;

        if (isset($object->attributes->hrefVariables))
        {
            foreach ($object->attributes->hrefVariables->content as $item)
            {
                if (is_array($item->content->value->content))
                {
                    foreach ($item->content->value->content as $key => $value)
                    {
                        $item->content->value->content[$key] = (is_object($value)) ? $value->content : $value;
                    }
                }

                $this->url_variables[$item->content->key->content] = new DataStructureElement($item);
            }
        }

        if (isset($object->attributes->data))
        {
            foreach ($object->attributes->data->content as $base)
            {
                foreach ($base->content as $item)
                {
                    $this->data_variables[$item->content->key->content] = new DataStructureElement($item);
                }
            }
        }

        if (isset($object->content[0]->content))
        {
            foreach ($object->content[0]->content as $item)
            {
                if ($item->element === 'httpRequest')
                {
                    $this->request = new HTTPRequest($this);
                    $this->request->parse($item);
                } elseif ($item->element === 'httpResponse')
                {
                    $response          = new HTTPResponse($this);
                    $this->responses[] = $response->parse($item);
                }
            }
        }

        return $this;
    }

    public function build_url()
    {
        $url = $this->href;
        foreach ($this->url_variables as $key => $value)
        {
            $urlvalue = $value['value'];
            if (is_array($value['value']) && !is_string($value['value']))
            {
                $urlvalue = $value['value'][0];
            }

            $url = preg_replace('/({\?' . $key . '})/', '?<var class="url-param">' . $key . '</var>=<var class="url-value">' . urlencode($urlvalue) . '</var>', $url);
            $url = preg_replace('/({\&' . $key . '})/', '&<var class="url-param">' . $key . '</var>=<var class="url-value">' . urlencode($urlvalue) . '</var>', $url);
            $url = preg_replace('/({' . $key . '})/', '<var class="url-value">' . urlencode($urlvalue) . '</var>', $url);
        }

        return $url;
    }

    public function get_method()
    {
        return (isset($this->request->method)) ? $this->request->method : 'NONE';
    }

    /**
     * Claim a structure as part of this transition
     *
     * @param string $objectName
     * @return void
     */
    public function spawn_object($objectName)
    {
        if(!in_array($objectName, $this->structures))
        {
            array_push($this->structures, $objectName);
        }

        var_dump($this->structures);
    }
}