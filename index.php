<?php
set_include_path(get_include_path().":".__DIR__);
$config = json_decode(file_get_contents(__DIR__."/config.json"));
require_once 'src/PHPDraft/Core/Autoloader.php';
use PHPDraft\In\ApibFileParser;
use PHPDraft\Parse\ApibToJson;
use PHPDraft\Parse\JsonToHTML;
$apib = new ApibFileParser($argv[1]);
$json = new ApibToJson($apib);
$json->parseToJson();
$html = new JsonToHTML($json);
echo $html->get_html();
?>
