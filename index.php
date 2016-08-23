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

if($argc < 1)
{
    file_put_contents('php://stderr', "Missing file to parse\n");
    exit(2);
}

$apib = new ApibFileParser($argv[1]);
$json = new ApibToJson($apib);
$json->parseToJson();
$html = new JsonToHTML($json);
$html->get_html();
?>
