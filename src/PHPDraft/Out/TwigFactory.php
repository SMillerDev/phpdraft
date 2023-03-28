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

        $twig->addFilter(new TwigFilter('method_icon', fn(string $string) => TemplateRenderer::get_method_icon($string)));
        $twig->addFilter(new TwigFilter('strip_link_spaces', fn(string $string) => TemplateRenderer::strip_link_spaces($string)));
        $twig->addFilter(new TwigFilter('response_status', fn(string $string) => TemplateRenderer::get_response_status((int) $string)));
        $twig->addFilter(new TwigFilter('status_reason', fn(int $code) => (new Httpstatus())->getReasonPhrase($code)));
        $twig->addFilter(new TwigFilter('minify_css', function (string $string) {
            $minify =  new Css();
            $minify->add($string);
            return $minify->minify();
        }));
        $twig->addFilter(new TwigFilter('minify_js', function (string $string) {
            $minify =  new JS();
            $minify->add($string);
            return $minify->minify();
        }));

        $twig->addTest(new TwigTest('enum_type', fn(object $object) => $object instanceof EnumStructureElement));
        $twig->addTest(new TwigTest('object_type', fn(object $object) => $object instanceof ObjectStructureElement));
        $twig->addTest(new TwigTest('array_type', fn(object $object) => $object instanceof ArrayStructureElement));
        $twig->addTest(new TwigTest('bool', fn($object) => is_bool($object)));
        $twig->addTest(new TwigTest('string', fn($object) => is_string($object)));
        $twig->addTest(new TwigTest('variable_type', fn(BasicStructureElement $object) => $object->is_variable));
        $twig->addTest(new TwigTest('inheriting', function (BasicStructureElement $object): bool {
            $options = array_merge(StructureElement::DEFAULTS, ['member', 'select', 'option', 'ref', 'T', 'hrefVariables']);
            return !(is_null($object->element) || in_array($object->element, $options, true));
        }));

        $twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {
            public function load(string $class): ?object
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
