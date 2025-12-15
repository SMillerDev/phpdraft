<?php

namespace PHPDraft\In;

use PHPDraft\Out\Sorting;
use PHPDraft\Out\Version;
use PHPDraft\Parse\ExecutionException;
use PHPDraft\Parse\ParserFactory;
use PHPDraft\Parse\ResourceException;
use splitbrain\phpcli\CLI as BaseCLI;
use splitbrain\phpcli\Options;

class CLI extends BaseCLI
{

    protected function setup(Options $options): void
    {
        $options->setHelp('Usage: phpdraft [options]');
        $options->registerOption('version', 'Print the version for PHPDraft.', 'v', false);
        $options->registerOption('file', 'Specifies the file to parse.', 'f', true);
        $options->registerOption('openapi', 'Output location for an OpenAPI file.', 'a', true);
        $options->registerOption('html', 'Output location for the rendered HTML.', 'r', true);
        $options->registerOption('yes', 'Always accept using the online mode.', 'y', true);
        $options->registerOption('online', 'Always use the online mode.', 'o', false);
        $options->registerOption('template', 'Specifies the template to use. (defaults to \'default\').', 't', true);
        $options->registerOption('sort', 'Sort displayed values [All|None|Structures|Webservices] (defaults to the way the objects are in the file).', 's', true);
        $options->registerOption('header-image', 'Specifies an image to display in the header.', 'i', true);
        $options->registerOption('css', 'Specifies a CSS file to include (value is put in a link element without checking).', 'c', true);
        $options->registerOption('javascript', 'Specifies a JS file to include (value is put in a script element without checking).', 'j', true);
        $options->registerOption('debug-json-file', 'Input a rendered JSON file for debugging.', '', true);
        $options->registerOption('debug-json', 'Input a rendered JSON text for debugging.', '', true);
    }

    /**
     * @throws ExecutionException
     */
    protected function main(Options $options): void
    {
        $args = $options->getOpt();
        if ($options->getOpt('version', NULL) !== NULL) {
            Version::version();
            throw new ExecutionException('', 0);
        }

        stream_set_blocking(STDIN, false);
        $stdin = stream_get_contents(STDIN);
        $file = $options->getOpt('file', NULL);
        if (!empty($stdin) && $file !== NULL) {
            throw new ExecutionException('ERROR: Passed data in both file and stdin', 2);
        } elseif (!empty($stdin) && $file === NULL) {
            $file = tempnam(sys_get_temp_dir(), 'phpdraft');
            file_put_contents($file, $stdin);
        }
        if ($file === NULL || $file === '')
        {
            throw new ExecutionException('ERROR: File does not exist', 200);
        }

        if (!($file !== NULL || $options->getOpt('debug-json-file') === FALSE || $options->getOpt('debug-json') === FALSE)) {
            throw new ExecutionException('Missing required option: file', 1);
        }

        define('THIRD_PARTY_ALLOWED', getenv('PHPDRAFT_THIRD_PARTY') !== '0');
        if ((isset($args['yes']) || isset($args['online'])) && THIRD_PARTY_ALLOWED) {
            define('DRAFTER_ONLINE_MODE', 1);
        }

        if (!isset($args['debug-json-file']) && !isset($args['debug-json'])) {
            $apib_parser = new ApibFileParser($file);
            $apib        = $apib_parser->parse();

            try {
                $parser = ParserFactory::getDrafter();
                $parser = $parser->init($apib);
                $data = $parser->parseToJson();
            } catch (ResourceException $exception) {
                throw new ExecutionException('No drafter available', 255, $exception);
            }
        } else {
            $json_string = $args['debug-json'] ?? file_get_contents($args['debug-json-file']);
            $data = json_decode($json_string);
        }

        if (isset($args['openapi'])) {
            $openapi = ParserFactory::getOpenAPI()->init($data);
            $openapi->write($args['openapi']);
        }

        $html          = ParserFactory::getJson()->init($data);
        $name          = 'PHPD_SORT_' . strtoupper($options->getOpt('sort', ''));
        $html->sorting = Sorting::${$name} ?? Sorting::PHPD_SORT_NONE->value;

        $color1        = getenv('COLOR_PRIMARY') === FALSE ? NULL : getenv('COLOR_PRIMARY');
        $color2        = getenv('COLOR_SECONDARY') === FALSE ? NULL : getenv('COLOR_SECONDARY');
        $colors        = (is_null($color1) || is_null($color2)) ? '' : '__' . $color1 . '__' . $color2;
        $html->build_html(
            $options->getOpt('template', 'default') . $colors,
            $args['header-image'] ?? NULL,
            $args['css'] ?? NULL,
            $args['javascript'] ?? NULL,
        );

        if (isset($args['html'])) {
            file_put_contents($args['html'], $html);
            return;
        }

        echo $html;
    }
}