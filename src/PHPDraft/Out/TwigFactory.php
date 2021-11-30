<?php

namespace PHPDraft\Out;

use Lukasoppermann\Httpstatus\Httpstatus;
use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;
use PHPDraft\Model\Elements\ArrayStructureElement;
use PHPDraft\Model\Elements\BasicStructureElement;
use PHPDraft\Model\Elements\EnumStructureElement;
use PHPDraft\Model\Elements\ObjectStructureElement;
use PHPDraft\Model\Elements\StructureElement;
use Twig\Environment;
use Twig\Extra\Markdown\DefaultMarkdown;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\Loader\LoaderInterface;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\TwigFilter;
use Twig\TwigTest;

class TwigFactory
{
    public static function get(LoaderInterface $loader): Environment
    {
        $twig = new Environment($loader);

        $twig->addFilter(new TwigFilter('minify_css', function ($string) {
            $minify =  new Css();
            $minify->add($string);
            return $minify->minify();
        }));
        $twig->addFilter(new TwigFilter('minify_js', function ($string) {
            $minify =  new JS();
            $minify->add($string);
            return $minify->minify();
        }));
        $twig->addFilter(new TwigFilter('method_icon', function ($string) {
            return TemplateRenderer::get_method_icon($string);
        }));
        $twig->addFilter(new TwigFilter('strip_link_spaces', function ($string) {
            return TemplateRenderer::strip_link_spaces($string);
        }));
        $twig->addFilter(new TwigFilter('response_status', function ($string) {
            return TemplateRenderer::get_response_status($string);
        }));
        $twig->addFilter(new TwigFilter('status_reason', function ($string) {
            return (new Httpstatus())->getReasonPhrase($string);
            ;
        }));
        $twig->addTest(new TwigTest('enum_type', function ($object) {
            return $object instanceof EnumStructureElement;
        }));
        $twig->addTest(new TwigTest('object_type', function ($object) {
            return $object instanceof ObjectStructureElement;
        }));
        $twig->addTest(new TwigTest('array_type', function ($object) {
            return $object instanceof ArrayStructureElement;
        }));
        $twig->addTest(new TwigTest('bool', function ($object) {
            return is_bool($object);
        }));
        $twig->addTest(new TwigTest('string', function ($object) {
            return is_string($object);
        }));

        $twig->addTest(new TwigTest('inheriting', function (BasicStructureElement $object) {
            $options = array_merge(StructureElement::DEFAULTS, ['member', 'select', 'option', 'ref', 'T', 'hrefVariables']);
            return !(is_null($object->element) || in_array($object->element, $options));
        }));
        $twig->addTest(new TwigTest('variable_type', function (BasicStructureElement $object) {
            return $object->is_variable;
        }));

        $twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {
            public function load(string $class)
            {
                if (MarkdownRuntime::class === $class) {
                    return new MarkdownRuntime(new DefaultMarkdown());
                }
                return null;
            }
        });
        $twig->addExtension(new MarkdownExtension());

        return $twig;
    }
}
