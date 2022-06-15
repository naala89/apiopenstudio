<?php

/**
 * Trait ConvertToArrayTrait.
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

use SoapBox\Formatter\Formatter;

/**
 * Trait ConvertToArrayTrait.
 *
 * Trait to cast an input value to array.
 */
trait ConvertToArrayTrait
{
    /**
     * Convert array to array.
     *
     * @param array $array
     *
     * @return array
     */
    public function fromArrayToArray(array $array): array
    {
        return $array;
    }

    /**
     * Convert boolean to array.
     *
     * @param bool $boolean
     *
     * @return array
     */
    public function fromBooleanToArray(bool $boolean): array
    {
        return [$boolean];
    }

    /**
     * Convert file to array.
     *
     * @param $file
     *
     * @throws ApiException
     */
    public function fromFileToArray($file)
    {
        throw new ApiException('Cannot cast file to array', 6, -1, 400);
    }

    /**
     * Convert float to array.
     *
     * @param float $float
     *
     * @return array
     */
    public function fromFloatToArray(float $float): array
    {
        return [$float];
    }

    /**
     * Convert HTML to array.
     *
     * @param string $html
     *
     * @return array
     */
    public function fromHtmlToArray(string $html): array
    {
        $convertHtml = new ConvertHtml();
        return $convertHtml->htmlToArray($html);
    }

    /**
     * Convert image to array.
     *
     * @param $image
     *
     * @throws ApiException
     */
    public function fromImageToArray($image)
    {
        throw new ApiException('Cannot cast image to array', 6, -1, 400);
    }

    /**
     * Convert integer to array.
     *
     * @param int $integer
     *
     * @return array
     */
    public function fromIntegerToArray(int $integer): array
    {
        return [$integer];
    }

    /**
     * Convert JSON string to array.
     *
     * @param $json
     *
     * @return array
     */
    public function fromJsonToArray($json): array
    {
        $formatter = Formatter::make($json, Formatter::JSON);
        $array = $formatter->toArray();
        return (!is_array($array) ? [$array] : $array);
    }

    /**
     * Convert text to array.
     *
     * @param string $text
     *
     * @return array
     */
    public function fromTextToArray(string $text): array
    {
        return [$text];
    }

    /**
     * Convert undefined to array.
     *
     * @param $data
     *
     * @return null
     */
    public function fromUndefinedToArray($data)
    {
        return null;
    }

    /**
     * Convert XML string to array.
     *
     * @param string $xml
     *
     * @return array
     */
    public function fromXmlToArray(string $xml): array
    {
        $formatter = Formatter::make($xml, Formatter::XML);
        return $formatter->toArray();
    }
}
