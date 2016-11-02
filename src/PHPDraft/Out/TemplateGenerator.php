<?php
/**
 * This file contains the TemplateGenerator.php
 *
 * @package PHPDraft\Out
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out;

use Michelf\MarkdownExtra;
use PHPDraft\Model\Category;
use PHPDraft\Model\Elements\DataStructureElement;

class TemplateGenerator
{
    /**
     * Type of sorting to do on objects
     *
     * @var int
     */
    public $sorting;
    /**
     * CSS Files to load
     *
     * @var array
     */
    public $css = [];
    /**
     * JS Files to load
     *
     * @var array
     */
    public $js = [];
    /**
     * JSON object of the API blueprint
     *
     * @var mixed
     */
    protected $categories = [];
    /**
     * The template file to load
     *
     * @var string
     */
    protected $template;
    /**
     * The image to use as a logo
     *
     * @var string
     */
    protected $image = null;
    /**
     * The base URl of the API
     *
     * @var
     */
    protected $base_data;
    /**
     * Structures used in all data
     *
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
        $this->image    = $image;
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
        $include = $this->find_include_file($this->template);
        if ($include === null) {
            file_put_contents('php://stderr', "Couldn't find template '$this->template'\n");
            exit(1);
        }

        //Prepare base data
        if (is_array($object->content[0]->content)) {
            foreach ($object->content[0]->attributes->meta as $meta) {
                $this->base_data[$meta->content->key->content] = $meta->content->value->content;
            }
            foreach ($object->content[0]->content as $value) {
                if ($value->element === 'copy') {
                    $this->base_data['DESC'] =
                        preg_replace('/(<\/?p>)/', '', MarkdownExtra::defaultTransform($value->content), 2);
                    continue;
                }

                $cat = new Category();
                $cat = $cat->parse($value);

                if ($value->meta->classes[0] === 'dataStructures') {
                    $this->base_structures = array_merge($this->base_structures, $cat->structures);
                } else {
                    $this->categories[] = $cat;
                }
            }

            $this->base_data['TITLE'] = $object->content[0]->meta->title;
        }
        if ($this->sorting === UI::$PHPD_SORT_OPT_ALL || $this->sorting === UI::$PHPD_SORT_OPT_STRUCTURES) {
            ksort($this->base_structures);
        }
        if ($this->sorting === UI::$PHPD_SORT_OPT_ALL || $this->sorting === UI::$PHPD_SORT_OPT_WEBSERVICES) {
            foreach ($this->categories as &$category) {
                usort($category->children, function ($a, $b) {
                    return strcmp($a->title, $b->title);
                });
            }
        }

        require_once $include;
    }

    /**
     * Get the path to a file to include
     *
     * @param string $template The name of the template to include
     * @param string $extension Extension of the file to include
     *
     * @return null|string File path or null if not found
     */
    function find_include_file($template, $extension = 'phtml')
    {
        $include = null;
        $fextension = '.'.$extension;
        if (stream_resolve_include_path('templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . $template . $fextension)) {
            $include = 'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . $template . $fextension;
            return $include;
        }
        if (stream_resolve_include_path('templates' . DIRECTORY_SEPARATOR . $template . $fextension)) {
            $include = 'templates' . DIRECTORY_SEPARATOR . $template . $fextension;
            return $include;
        }
        if (stream_resolve_include_path($template . DIRECTORY_SEPARATOR . $template . $fextension)) {
            $include = $template . DIRECTORY_SEPARATOR . $template . $fextension;
            return $include;
        }

        if (stream_resolve_include_path($template . $fextension)) {
            $include = $template . $fextension;
            return $include;
        }

        if (stream_resolve_include_path('PHPDraft/Out/HTML/' . $template . $fextension)) {
            $include = 'PHPDraft/Out/HTML/' . $template . $fextension;
            return $include;
        }

        if ($include === null && $extension === 'phtml') {
            return $this->find_include_file('default', $extension);
        }
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
        switch (strtolower($method)) {
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
        if ($response <= 299) {
            return 'text-success';
        } elseif ($response > 299 && $response <= 399) {
            return 'text-warning';
        } else {
            return 'text-error';
        }
    }

    function strip_link_spaces($key)
    {
        return str_replace(' ', '-', strtolower($key));
    }

}