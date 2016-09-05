<?php
/**
 * Set up include path for source handling
 */
set_include_path(get_include_path().":".__DIR__.'/src/');
$config = json_decode(file_get_contents(__DIR__."/config.json"));

/**
 * Set up required classes (with the autoloader)
 */
require_once 'PHPDraft/Core/Autoloader.php';
use PHPDraft\In\ApibFileParser;
use PHPDraft\Parse\ApibToJson;
use PHPDraft\Parse\JsonToHTML;

$options = getopt("f:t::i::h");
if(!isset($argv[1]))
{
    file_put_contents('php://stderr', 'Not enough arguments'.PHP_EOL);
    help();
    exit(1);
}

if (boolval(preg_match('/^\-/',$argv[1])))
{
    if (isset($options['h']))
    {
        help();
        exit(0);
    }
    elseif (isset($options['f']))
    {
        $file = $options['f'];
    }
    else
    {
        file_put_contents('php://stderr', 'No file to parse'.PHP_EOL);
        exit(1);
    }
}
else
{
    $file = $argv[1];
}

$template = (isset($options['t']) && $options['t']) ? $options['t']: 'default';
$image = (isset($options['i']) && $options['i']) ? $options['i']: NULL;

$apib = new ApibFileParser($file);
$json = new ApibToJson($apib);
$html = new JsonToHTML($json->parseToJson());
$html->get_html($template, $image);

function help()
{
    echo 'This is a parser for API Blueprint files in PHP.'.PHP_EOL.PHP_EOL;
    echo "The following options can be used:.\n";
    echo "\t-f\tSpecifies the file to parse.\n";
    echo "\t-t\tSpecifies the template to use. (defaults to 'default')\n";
    echo "\t-h\tDisplays this text.\n";
}
?>
