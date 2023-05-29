<?php

declare(strict_types=1);

/**
 * This file contains the TemplateGenerator.php.
 *
 * @package PHPDraft\Out
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Out;

use Lukasoppermann\Httpstatus\Httpstatus;
use PHPDraft\Model\Category;
use PHPDraft\Model\Elements\ArrayStructureElement;
use PHPDraft\Model\Elements\ElementStructureElement;
use PHPDraft\Model\Elements\EnumStructureElement;
use PHPDraft\Model\Elements\ObjectStructureElement;
use PHPDraft\Parse\ExecutionException;
use Twig\Environment;
use Twig\Extra\Markdown\DefaultMarkdown;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\TwigFilter;
use Twig\TwigTest;

class TemplateRenderer extends BaseTemplateRenderer
{
    /**
     * TemplateGenerator constructor.
     *
     * @param string      $template Name of the template to load
     * @param string|null $image    Image to use as Logo
     */
    public function __construct(string $template, ?string $image)
    {
        $template_parts             = explode('__', $template);
        $this->template             = $template_parts[0];
        $this->base_data['COLOR_1'] = $template_parts[1] ?? 'green';
        $this->base_data['COLOR_2'] = $template_parts[2] ?? 'light_green';
        $this->image                = $image;
        $this->sorting              = Sorting::PHPD_SORT_NONE;
    }

    /**
     * Pre-parse objects needed and print HTML.
     *
     * @param object $object JSON to parse from
     *
     * @return string
     *
     * @throws ExecutionException When template is not found
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function get(object $object): string
    {
        $include = $this->find_include_file($this->template, 'twig', false);
        if ($include === null) {
            throw new ExecutionException("Couldn't find template '$this->template'", 1);
        }

        $this->parse_base_data($object);

        if (Sorting::sortStructures($this->sorting)) {
            ksort($this->base_structures);
        }

        if (Sorting::sortServices($this->sorting)) {
            usort($this->categories, fn($a, $b) => strcmp($a->title, $b->title));
            foreach ($this->categories as $category) {
                usort($category->children, fn($a, $b)=> strcmp($a->title, $b->title));
            }
        }

        $loader   = new FilesystemLoader([]);
        $dirpath = stream_resolve_include_path(dirname($include));
        if ($dirpath !== FALSE) {
            $loader->addPath($dirpath);
        }
        $twig_path = stream_resolve_include_path(dirname($this->find_include_file('default', 'twig')));
        if ($twig_path !== FALSE) {
            $loader->addPath($twig_path);
        }

        $twig     = TwigFactory::get($loader);
        $template = $twig->load('main.twig');

        $extras = array_filter(
            $this->base_data,
            fn($value) => !in_array($value, ['HOST', 'TITLE', 'ALT_HOST', 'FORMAT', 'DESC', 'COLOR_1', 'COLOR_2'], true),
            ARRAY_FILTER_USE_KEY
        );
        $extras['host'] = $this->base_data['HOST'] ?? null;

        return $template->render([
            'data' => $this->base_data,
            'extra_data' => $extras,
            'structures' => $this->base_structures,
            'categories' => $this->categories,
            'js' => $this->js,
            'css' => $this->css,
            'image' => $this->image,
            'template_css' => file_get_contents($this->find_include_file($this->template, 'css'), true),
            'template_js' => file_get_contents($this->find_include_file($this->template, 'js'), true),
        ]);
    }

    /**
     * Parse base data
     *
     * @param object $object
     */
    private function parse_base_data(object $object): void
    {
        //Prepare base data
        if (!is_array($object->content[0]->content)) {
            return;
        }

        $this->base_data['TITLE'] = $object->content[0]->meta->title->content ?? '';

        foreach ($object->content[0]->attributes->metadata->content as $meta) {
            $this->base_data[$meta->content->key->content] = $meta->content->value->content;
        }

        foreach ($object->content[0]->content as $value) {
            if ($value->element === 'copy') {
                $this->base_data['DESC'] = $value->content;
                continue;
            }

            $cat = new Category();
            $cat = $cat->parse($value);

            if (($value->meta->classes->content[0]->content ?? null) === 'dataStructures') {
                $this->base_structures = array_merge($this->base_structures, $cat->structures);
            } else {
                $this->categories[] = $cat;
            }
        }
    }

    /**
     * Get the path to a file to include.
     *
     * @param string $template  The name of the template to include
     * @param string $extension Extension of the file to include
     *
     * @return null|string File path or null if not found
     */
    public function find_include_file(string $template, string $extension = 'twig', bool $fallback = true): ?string
    {
        $includes = [
            'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . $template . ".{$extension}",
            'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . 'main' . ".{$extension}",
            'templates' . DIRECTORY_SEPARATOR . $template . ".{$extension}",
            $template . DIRECTORY_SEPARATOR . $template . ".{$extension}",
            $template . ".{$extension}",
            'PHPDraft/Out/HTML/' . $template . DIRECTORY_SEPARATOR . $template . ".{$extension}",
            'PHPDraft/Out/HTML/' . $template . DIRECTORY_SEPARATOR . 'main' . ".{$extension}",
        ];
        foreach ($includes as $include) {
            if (stream_resolve_include_path($include) === FALSE) {
                continue;
            }
            return $include;
        }

        if (in_array($extension, ['twig', 'js', 'css'], true) && $template !== 'default' && $fallback === true) {
            return $this->find_include_file('default', $extension);
        }

        return null;
    }

    /**
     * Get an icon for a specific HTTP Method.
     *
     * @param string $method HTTP method
     *
     * @return string class to represent the HTTP Method
     */
    public static function get_method_icon(string $method): string
    {
        $class = ['fas', strtoupper($method)];
        $class[] = match (strtoupper($method)) {
            'POST' => 'fa-plus-square',
            'PUT' => 'fa-pen-square',
            'GET' => 'fa-arrow-circle-down',
            'DELETE' => 'fa-minus-square',
            'HEAD' => 'fa-info',
            'CONNECT' => 'fa-ethernet',
            'OPTIONS' => 'fa-sliders-h',
            'TRACE' => 'fa-route',
            'PATCH' => 'fa-band-aid',
            default => ''
        };

        return trim(join(' ', $class));
    }

    /**
     * Get a bootstrap class to represent the HTTP return code range.
     *
     * @param int $response HTTP return code
     *
     * @return string Class to use
     */
    public static function get_response_status(int $response): string
    {
        $http = new Httpstatus();
        return match ($http->getResponseClass($response)) {
            Httpstatus::CLASS_SUCCESS => 'text-success',
            Httpstatus::CLASS_REDIRECTION => 'text-warning',
            default => 'text-error',
        };
    }

    /**
     * Strip spaces from links to objects.
     *
     * @param string $key key with potential spaces
     *
     * @return string key without spaces
     */
    public static function strip_link_spaces(string $key): string
    {
        return str_replace(' ', '-', strtolower($key));
    }
}
