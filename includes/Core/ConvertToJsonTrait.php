<?php

/**
 * Trait ConvertToJsonTrait.
 *
 * @package    ApiOpenStudio\Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

use Exception;
use SimpleXMLElement;
use SoapBox\Formatter\Formatter;

/**
 * Trait ConvertToJsonTrait.
 *
 * Class to cast an input value to JSON.
 */
trait ConvertToJsonTrait
{
    /**
     * Convert array to JSON string.
     *
     * @param array|null $array
     *
     * @return string|null
     */
    public function fromArrayToJson(?array $array): ?string
    {
        if (is_null($array)) {
            return null;
        }
        return json_encode($array);
    }

    /**
     * Convert boolean to JSON string.
     *
     * @param ?bool $boolean
     *
     * @return ?bool
     */
    public function fromBooleanToJson(?bool $boolean): ?bool
    {
        return $boolean;
    }

    /**
     * Convert file to JSON string.
     *
     * @param $file
     *
     * @throws ApiException
     */
    public function fromFileToJson($file)
    {
        throw new ApiException('Cannot cast file to JSON', 6, -1, 400);
    }

    /**
     * Convert float to JSON string.
     *
     * @param float|string|null $float
     *
     * @return float|string|null
     */
    public function fromFloatToJson($float)
    {
        if (is_infinite($float)) {
            $float = $float < 0 ? '-Infinity' : 'Infinity';
        } elseif (is_nan($float)) {
            $float = 'NAN';
        }
        return $float;
    }

    /**
     * Convert an HTML string to JSON string.
     *
     * @param string $html
     *
     * @return string
     */
    public function fromHtmlToJson(string $html): string
    {
        $convertHtml = new ConvertHtml();
        return $convertHtml->htmlToJson($html);
    }

    /**
     * Convert an image string to JSON string.
     *
     * @param $image
     *
     * @return string|null
     */
    public function fromImageToJson($image): ?string
    {
        return $image;
    }

    /**
     * Convert integer to JSON string.
     *
     * @param int|string|null $integer
     *
     * @return int|string|null
     */
    public function fromIntegerToJson($integer)
    {
        if (is_infinite($integer)) {
            $integer = $integer < 0 ? '-Infinity' : 'Infinity';
        } elseif (is_nan($integer)) {
            $integer = 'NAN';
        }
        return $integer;
    }

    /**
     * Convert JSON string to JSON string.
     *
     * @param $json
     *
     * @return mixed
     */
    public function fromJsonToJson($json)
    {
        return $json;
    }

    /**
     * Convert text to JSON string.
     *
     * @param ?string $text
     *
     * @return string|null
     */
    public function fromTextToJson(?string $text): ?string
    {
        return $text;
    }

    /**
     * Convert undefined to JSON.
     *
     * @param $data
     *
     * @return null
     */
    public function fromUndefinedToJson($data)
    {
        return null;
    }

    /**
     * Convert XML string to JSON string.
     *
     * @param string $xml
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromXmlToJson(string $xml): string
    {
        try {
            $sxe = new SimpleXMLElement($xml);
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), 6, -1, 500);
        }
        $baseTagName = $sxe->getName();
        $formatter = Formatter::make($xml, Formatter::XML);
        $array = json_decode($formatter->toJson(), true);
        return json_encode([$baseTagName => $array]);
    }
}
