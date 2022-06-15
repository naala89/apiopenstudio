<?php

/**
 * Trait ConvertToUndefinedTrait.
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
 * Trait ConvertToUndefinedTrait.
 *
 * Trait to cast an input value to undefined.
 */
trait ConvertToUndefinedTrait
{
    /**
     * Convert array to undefined.
     *
     * @param array $array
     *
     * @return null
     */
    public function fromArrayToUndefined(array $array)
    {
        return null;
    }

    /**
     * Convert boolean to undefined.
     *
     * @param bool $boolean
     *
     * @return null
     */
    public function fromBooleanToUndefined(bool $boolean)
    {
        return null;
    }

    /**
     * Convert file to undefined.
     *
     * @param $file
     *
     * @return null
     */
    public function fromFileToUndefined($file)
    {
        return null;
    }

    /**
     * Convert float to undefined.
     *
     * @param float $float
     *
     * @return null
     */
    public function fromFloatToUndefined(float $float)
    {
        return null;
    }

    /**
     * Convert HTML to undefined.
     *
     * @param string $html
     *
     * @return null
     */
    public function fromHtmlToUndefined(string $html)
    {
        return null;
    }

    /**
     * Convert image to undefined.
     *
     * @param $image
     *
     * @return null
     */
    public function fromImageToUndefined($image)
    {
        return null;
    }

    /**
     * Convert integer to undefined.
     *
     * @param int $integer
     *
     * @return null
     */
    public function fromIntegerToUndefined(int $integer)
    {
        return null;
    }

    /**
     * Convert JSON to undefined.
     *
     * @param string $json
     *
     * @return null
     */
    public function fromJsonToUndefined(string $json)
    {
        return null;
    }

    /**
     * Convert text to undefined.
     *
     * @param string $string
     *
     * @return null
     */
    public function fromTextToUndefined(string $string)
    {
        return null;
    }

    /**
     * Convert undefined to undefined.
     *
     * @param $data
     *
     * @return null
     */
    public function fromUndefinedToUndefined($data)
    {
        return null;
    }

    /**
     * Convert XML to undefined.
     *
     * @param string $xml
     *
     * @return null
     */
    public function fromXmlToUndefined(string $xml)
    {
        return null;
    }
}
