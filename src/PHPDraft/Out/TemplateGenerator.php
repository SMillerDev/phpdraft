<?php
/**
 * This file contains the TemplateGenerator.php
 *
 * @package PHPDraft\Out
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out;

use Michelf\Markdown;
use PHPDraft\Model\Category;
use PHPDraft\Model\Elements\DataStructureElement;

class TemplateGenerator
{
    /**
     * JSON object of the API blueprint
     * @var mixed
     */
    protected $categories = [];

    /**
     * The template file to load
     * @var string
     */
    protected $template;

    /**
     * The image to use as a logo
     * @var string
     */
    protected $image;

    /**
     * The base URl of the API
     * @var
     */
    protected $base_data;

    /**
     * Structures used in all data
     * @var DataStructureElement[]
     */
    protected $base_structures = [];

    /**
     * TemplateGenerator constructor.
     *
     * @param string $template name of the template to load
     * @param string $image    Image to use as Logo
     */
    public function __construct($template, $image)
    {
        $this->template = $template;
        $this->image = $image;
    }

    /**
     * Pre-parse objects needed and print HTML
     *
     * @param mixed $object JSON to parse from
     *
     * @return void
     */
    public function get($object)
    {
        $include = NULL;
        if (stream_resolve_include_path($this->template . DIRECTORY_SEPARATOR . $this->template . '.php'))
        {
            $include = $this->template . DIRECTORY_SEPARATOR . $this->template . '.php';
        }

        if (stream_resolve_include_path($this->template . '.php'))
        {
            $include = $this->template . '.php';
        }

        if (stream_resolve_include_path('PHPDraft/Out/HTML/' . $this->template . '.php'))
        {
            $include = 'PHPDraft/Out/HTML/' . $this->template . '.php';
        }
        if ($include === NULL)
        {
            file_put_contents('php://stderr', "Couldn't find template '$this->template'\n");
            exit(1);
        }

        //Prepare base data
        if (is_array($object->content[0]->content))
        {
            foreach ($object->content[0]->attributes->meta as $meta)
            {
                $this->base_data[$meta->content->key->content] = $meta->content->value->content;
            }
            foreach ($object->content[0]->content as $value)
            {
                if ($value->element === 'copy')
                {
                    $this->base_data['DESC'] =
                        preg_replace('/(<\/?p>)/', '', Markdown::defaultTransform($value->content), 2);
                    continue;
                }

                $cat                = new Category();
                $this->categories[] = $cat->parse($value);

                if ($value->meta->classes[0] === 'dataStructures')
                {
                    $this->base_structures = $cat->structures;
                }
            }

            $this->base_data['TITLE'] = $object->content[0]->meta->title;
        }

        include_once $include;
    }

    /**
     * Get an icon for a specific HTTP Method
     *
     * @param string $method HTTP method
     *
     * @return string class to represent the HTTP Method
     */
    function get_method_icon($method)
    {
        switch (strtolower($method))
        {
            case 'post':
                $class = 'glyphicon glyphicon-plus';
                break;
            case 'put':
                $class = 'glyphicon glyphicon-pencil';
                break;
            case 'get':
                $class = 'glyphicon glyphicon-arrow-down';
                break;
            case 'delete':
                $class = 'glyphicon glyphicon-remove';
                break;
            default:
                $class = '';
        }

        return $class . ' ' . $method;
    }

    /**
     * Get a bootstrap class to represent the HTTP return code range
     *
     * @param int $response HTTP return code
     *
     * @return string Class to use
     */
    function get_response_status($response)
    {
        if ($response <= 299)
        {
            return 'text-success';
        }
        elseif ($response > 299 && $response <= 399)
        {
            return 'text-warning';
        }
        else
        {
            return 'text-error';
        }
    }

    function strip_link_spaces($key)
    {
        return str_replace(' ', '__', strtolower($key));
    }

}