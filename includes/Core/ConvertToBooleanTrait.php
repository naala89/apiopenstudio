<?php

/**
 * Trait ConvertToBooleanTrait.
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
 * Trait ConvertToBooleanTrait.
 *
 * Trait to cast an input value to boolean.
 */
trait ConvertToBooleanTrait
{
    /**
     * Convert array to boolean.
     *
     * @param array $array
     *
     * @throws ApiException
     */
    public function fromArrayToBoolean(array $array)
    {
        throw new ApiException('Cannot cast array to boolean', 6, -1, 400);
    }

    /**
     * Convert boolean to boolean.
     *
     * @param bool $boolean
     *
     * @return boolean
     */
    public function fromBooleanToBoolean(bool $boolean): bool
    {
        return $boolean;
    }

    /**
     * Convert file to boolean.
     *
     * @param $file
     *
     * @throws ApiException
     */
    public function fromFileToBoolean($file)
    {
        throw new ApiException('Cannot cast file to boolean', 6, -1, 400);
    }

    /**
     * Convert float to boolean.
     *
     * @param float $float
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function fromFloatToBoolean(float $float): bool
    {
        $boolean = filter_var($float, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($boolean === null) {
            throw new ApiException('Cannot cast float to boolean', 6, -1, 400);
        }
        return $boolean;
    }

    /**
     * Convert HTML to boolean.
     *
     * @param string $html
     *
     * @throws ApiException
     */
    public function fromHtmlToBoolean(string $html)
    {
        throw new ApiException('Cannot cast HTML to boolean', 6, -1, 400);
    }

    /**
     * Convert image to boolean.
     *
     * @param $image
     *
     * @throws ApiException
     */
    public function fromImageToBoolean($image)
    {
        throw new ApiException('Cannot cast image to boolean', 6, -1, 400);
    }

    /**
     * Convert integer to boolean.
     *
     * @param int $integer
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function fromIntegerToBoolean(int $integer): bool
    {
        $boolean = filter_var($integer, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($boolean === null) {
            throw new ApiException('Cannot cast integer to boolean', 6, -1, 400);
        }
        return $boolean;
    }

    /**
     * Convert JSON string to boolean.
     *
     * @param string $json
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function fromJsonToBoolean(string $json): bool
    {
        $boolean = filter_var($json, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($boolean === null) {
            throw new ApiException('Cannot cast JSON to boolean', 6, -1, 400);
        }
        return $boolean;
    }

    /**
     * Convert text to boolean.
     *
     * @param string $text
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function fromTextToBoolean(string $text): bool
    {
        $boolean = filter_var($text, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($boolean === null) {
            throw new ApiException('Cannot cast text to boolean', 6, -1, 400);
        }
        return $boolean;
    }

    /**
     * Convert undefined to boolean.
     *
     * @param $data
     *
     * @return null
     */
    public function fromUndefinedToBoolean($data)
    {
        return null;
    }

    /**
     * Convert XML to boolean.
     *
     * @param string $xml
     *
     * @throws ApiException
     */
    public function fromXmlToBoolean(string $xml)
    {
        throw new ApiException('Cannot cast XML to boolean', 6, -1, 400);
    }
}
