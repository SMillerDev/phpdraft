<?php
/**
 * Set up include path for source handling
 */
set_include_path(get_include_path() . ":" . __DIR__ . '/src/');

/**
 * Set up required classes (with the autoloader)
 */
//require_once 'PHPDraft/Core/Autoloader.php';
require_once 'vendor/autoload.php';
use PHPDraft\In\ApibFileParser;
use PHPDraft\Out\UI;
use PHPDraft\Parse\Drafter;
use PHPDraft\Parse\DrafterAPI;
use PHPDraft\Parse\JsonToHTML;

define('VERSION', '0');
$values = UI::main($argv);

$apib = new ApibFileParser($values['file']);
$apib = $apib->parse();

$json = new DrafterAPI($apib);
if (!(defined('DRAFTER_ONLINE_MODE') && DRAFTER_ONLINE_MODE === 1))
{
    try
    {
        $json = new Drafter($apib);
    }
    catch (RuntimeException $exception)
    {
        file_put_contents('php://stderr', $exception->getMessage() . "\n");
        $options = [
            'y' => 'Yes',
            'n' => 'No',
        ];
        $answer  = UI::ask('Do you want to use the online version? [y/n]', $options, 'y');
        if (!$answer)
        {
            file_put_contents('php://stderr', 'Could not find a suitable drafter version');
            exit(1);
        }
    }
}

$html          = new JsonToHTML($json->parseToJson());
$html->sorting = $values['sorting'];
$generator     = $html->get_html($values['template'], $values['image'], $values['css'], $values['js']);


function phpdraft_var_dump(...$vars)
{
    if (defined('__PHPDRAFT_PHAR__'))
    {
        return;
    }
    echo '<pre>';
    foreach ($vars as $var)
    {
        var_dump($var);
    }
    echo '</pre>';
}
