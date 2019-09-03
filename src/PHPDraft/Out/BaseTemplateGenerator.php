<?php

/**
 * This file contains the BaseTemplateGenerator.php.
 *
 * @package PHPDraft\Out
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out;

use Lukasoppermann\Httpstatus\Httpstatus;
use PHPDraft\Model\Elements\ObjectStructureElement;

abstract class BaseTemplateGenerator
{
    /**
     * Type of sorting to do on objects.
     *
     * @var int
     */
    public $sorting;
    /**
     * CSS Files to load.
     *
     * @var array
     */
    public $css = [];
    /**
     * JS Files to load.
     *
     * @var array
     */
    public $js = [];
    /**
     * JSON object of the API blueprint.
     *
     * @var mixed
     */
    protected $categories = [];
    /**
     * The template file to load.
     *
     * @var string
     */
    protected $template;
    /**
     * The image to use as a logo.
     *
     * @var string|null
     */
    protected $image = null;
    /**
     * The base data of the API.
     *
     * @var array
     */
    protected $base_data;
    /**
     * The Http Status resolver.
     *
     * @var Httpstatus
     */
    protected $http_status;
    /**
     * Structures used in all data.
     *
     * @var ObjectStructureElement[]
     */
    protected $base_structures = [];

    /**
     * Get an icon for a specific HTTP Method.
     *
     * @param string $method HTTP method
     *
     * @return string class to represent the HTTP Method
     */
    public function get_method_icon(string $method): string
    {
        $class = ['fas', strtoupper($method)];
        switch (strtolower($method)) {
            case 'post':
                $class[] = 'fa-plus-square';
                break;
            case 'put':
                $class[] = 'fa-pen-square';
                break;
            case 'get':
                $class[] = 'fa-arrow-circle-down';
                break;
            case 'delete':
                $class[] = 'fa-minus-square';
                break;
            case 'head':
                $class[] = 'fa-info';
                break;
            case 'connect':
                $class[] = 'fa-ethernet';
                break;
            case 'options':
                $class[] = 'fa-sliders-h';
                break;
            case 'trace':
                $class[] = 'fa-route';
                break;
            case 'patch':
                $class[] = 'fa-band-aid';
                break;
            default:
                break;
        }

        return join(' ', $class);
    }

    /**
     * Get a bootstrap class to represent the HTTP return code range.
     *
     * @param int $response HTTP return code
     *
     * @return string Class to use
     */
    public function get_response_status(int $response): string
    {
        if ($response <= 299) {
            return 'text-success';
        } elseif ($response > 299 && $response <= 399) {
            return 'text-warning';
        } else {
            return 'text-error';
        }
    }

    /**
     * Strip spaces from links to objects.
     *
     * @param string $key key with potential spaces
     *
     * @return string key without spaces
     */
    public function strip_link_spaces(string $key): string
    {
        return str_replace(' ', '-', strtolower($key));
    }
}
