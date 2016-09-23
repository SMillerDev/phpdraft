<?php
/**
 * Set up include path for source handling
 */
set_include_path(get_include_path().":".__DIR__.'/src/');

/**
 * Set up required classes (with the autoloader)
 */
require_once 'PHPDraft/Core/Autoloader.php';
use PHPDraft\In\ApibFileParser;
use PHPDraft\Out\UI;
use PHPDraft\Parse\Drafter;
use PHPDraft\Parse\JsonToHTML;

define('VERSION', '0');

$values = UI::main($argv);

$apib = new ApibFileParser($values['file']);
$json = new Drafter($apib);
$html = new JsonToHTML($json->parseToJson());
$html->get_html($values['template'], $values['image']);


?>
