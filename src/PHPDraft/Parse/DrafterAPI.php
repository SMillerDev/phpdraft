<?php

declare(strict_types=1);

/**
 * This file contains the DrafterAPI.php.
 *
 * @package PHPDraft\Parse
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Parse;

use PHPDraft\In\ApibFileParser;

class DrafterAPI extends BaseParser
{
    /**
     * ApibToJson constructor.
     *
     * @param ApibFileParser $apib API Blueprint text
     *
     * @return \PHPDraft\Parse\BaseParser
     */
    public function init(ApibFileParser $apib): BaseParser
    {
        parent::init($apib);

        return $this;
    }

    /**
     * Parses the apib for the selected method.
     *
     * @return void
     */
    protected function parse(): void
    {
        $ch = self::curl_init_drafter($this->apib->content());

        $response = curl_exec($ch);

        if (curl_errno($ch) !== 0) {
            throw new ResourceException('Drafter webservice failed to parse input', 1);
        }

        $this->json = json_decode($response);
    }

    /**
     * Init curl for drafter webservice.
     *
     * @param string $message API blueprint to parse
     *
     * @return false|resource
     */
    public static function curl_init_drafter(string $message)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.apiblueprint.org/parser');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/vnd.apiblueprint',
            'Accept: application/vnd.refract.parse-result+json',
        ]);

        return $ch;
    }

    /**
     * Check if a given parser is available.
     *
     * @return bool
     */
    public static function available(): bool
    {
        if (!defined('DRAFTER_ONLINE_MODE') || DRAFTER_ONLINE_MODE !== 1) {
            return false;
        }

        $ch = self::curl_init_drafter('# Hello API
## /message
### GET
            + Response 200 (text/plain)

        Hello World!');

        curl_exec($ch);

        if (curl_errno($ch) !== CURLE_OK) {
            return false;
        }
        curl_close($ch);

        return true;
    }
}
