<?php

/**
 * Trait ConvertToEmptyTrait.
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
 * Trait ConvertToEmptyTrait.
 *
 * Trait to cast an input value to empty.
 */
trait ConvertToEmptyTrait
{
    /**
     * Convert array to empty.
     *
     * @param array $array
     *
     * @return null
     */
    public function fromArrayToEmpty(array $array)
    {
        return null;
    }

    /**
     * Convert boolean to empty.
     *
     * @param bool $boolean
     *
     * @return null
     */
    public function fromBooleanToEmpty(bool $boolean)
    {
        return null;
    }

    /**
     * Convert empty to empty.
     *
     * @param $data
     *
     * @return null
     */
    public function fromEmptyToEmpty($data)
    {
        return null;
    }

    /**
     * Convert file to empty.
     *
     * @param $file
     *
     * @return null
     */
    public function fromFileToEmpty($file)
    {
        return null;
    }

    /**
     * Convert float to empty.
     *
     * @param float $float
     *
     * @return null
     */
    public function fromFloatToEmpty(float $float)
    {
        return null;
    }

    /**
     * Convert HTML to empty.
     *
     * @param string $html
     *
     * @return null
     */
    public function fromHtmlToEmpty(string $html)
    {
        return null;
    }

    /**
     * Convert image to empty.
     *
     * @param $image
     *
     * @return null
     */
    public function fromImageToEmpty($image)
    {
        return null;
    }

    /**
     * Convert integer to empty.
     *
     * @param int $integer
     *
     * @return null
     */
    public function fromIntegerToEmpty(int $integer)
    {
        return null;
    }

    /**
     * Convert JSON to empty.
     *
     * @param string $json
     *
     * @return null
     */
    public function fromJsonToEmpty(string $json)
    {
        return null;
    }

    /**
     * Convert text to empty.
     *
     * @param string $string
     *
     * @return null
     */
    public function fromTextToEmpty(string $string)
    {
        return null;
    }

    /**
     * Convert XML to empty.
     *
     * @param string $xml
     *
     * @return null
     */
    public function fromXmlToEmpty(string $xml)
    {
        return null;
    }
}
