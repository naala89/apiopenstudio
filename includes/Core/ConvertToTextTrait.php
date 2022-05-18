<?php

/**
 * Trait ConvertToTextTrait.
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

/**
 * Trait ConvertToTextTrait.
 *
 * Trait to cast an input value to text.
 */
trait ConvertToTextTrait
{
    /**
     * Convert array to text.
     *
     * @param array|null $array
     *
     * @return null
     *
     * @throws ApiException
     */
    public function fromArrayToText(?array $array)
    {
        if (is_null($array)) {
            return null;
        }
        throw new ApiException('Cannot cast array to text', 6, -1, 400);
    }

    /**
     * Convert boolean to text.
     *
     * @param ?bool $boolean
     *
     * @return string
     */
    public function fromBooleanToText(?bool $boolean): string
    {
        return var_export($boolean, true);
    }

    /**
     * Convert file to text.
     *
     * @param $file
     *
     * @return string
     */
    public function fromFileToText($file): string
    {
        return $file;
    }

    /**
     * Convert float to text.
     *
     * @param ?float $float
     *
     * @return string
     */
    public function fromFloatToText(?float $float): string
    {
        return var_export($float, true);
    }

    /**
     * Convert HTML to text.
     *
     * @param string $html
     *
     * @return string
     */
    public function fromHtmlToText(string $html): string
    {
        return $html;
    }

    /**
     * Convert image to text.
     *
     * @param $image
     *
     * @return string
     */
    public function fromImageToText($image): string
    {
        return $image;
    }

    /**
     * Convert integer to text.
     *
     * @param int|float|null $integer
     *
     * @return string
     */
    public function fromIntegerToText($integer): string
    {
        return var_export($integer, true);
    }

    /**
     * Convert JSON to text.
     *
     * @param string $json
     *
     * @return string
     */
    public function fromJsonToText(string $json): string
    {
        return $json == '""' ? '' : $json;
    }

    /**
     * Convert text to text.
     *
     * @param string $text
     *
     * @return string
     */
    public function fromTextToText(string $text): string
    {
        return $text;
    }

    /**
     * Convert Undefined to text.
     *
     * @param $data
     *
     * @return null
     */
    public function fromUndefinedToText($data)
    {
        return null;
    }

    /**
     * Convert XML to text.
     *
     * @param string $xml
     *
     * @return string
     */
    public function fromXmlToText(string $xml): string
    {
        return $xml;
    }
}
