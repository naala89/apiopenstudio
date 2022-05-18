<?php

/**
 * Trait ConvertToIntegerTrait.
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
 * Trait ConvertToIntegerTrait.
 *
 * Trait to cast an input value to integer.
 */
trait ConvertToIntegerTrait
{
    /**
     * Convert an array to an integer.
     *
     * @param array $array
     *
     * @throws ApiException
     */
    public function fromArrayToInteger(array $array)
    {
        throw new ApiException('Cannot cast array to integer', 6, -1, 400);
    }

    /**
     * Convert a boolean to an integer.
     *
     * @param bool $boolean
     *
     * @return int
     */
    public function fromBooleanToInteger(bool $boolean): int
    {
        return (int) $boolean;
    }

    /**
     * Convert a file to an integer.
     *
     * @param $file
     *
     * @throws ApiException
     */
    public function fromFileToInteger($file)
    {
        throw new ApiException('Cannot cast file to integer', 6, -1, 400);
    }

    /**
     * Convert a float to an integer.
     *
     * @param float $float
     *
     * @return int|float
     *
     * @throws ApiException
     */
    public function fromFloatToInteger(float $float)
    {
        if (is_infinite($float)) {
            return $float < 0 ? -INF : INF;
        }
        if (is_nan($float)) {
            return NAN;
        }
        $integer = filter_var($float, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if ($integer === null) {
            throw new ApiException('Cannot cast float to integer', 6, -1, 400);
        }
        return $integer;
    }

    /**
     * Convert an HTML string to an integer.
     *
     * @param string $html
     *
     * @throws ApiException
     */
    public function fromHtmlToInteger(string $html)
    {
        throw new ApiException('Cannot cast HTML to integer', 6, -1, 400);
    }

    /**
     * Convert an image to an integer.
     *
     * @param $image
     *
     * @throws ApiException
     */
    public function fromImageToInteger($image)
    {
        throw new ApiException('Cannot cast image to integer', 6, -1, 400);
    }

    /**
     * Convert an integer to an integer.
     *
     * @param int $integer
     *
     * @return int
     */
    public function fromIntegerToInteger(int $integer): int
    {
        return $integer;
    }

    /**
     * Convert a JSON string to an integer.
     *
     * @param string $json
     *
     * @return float|int|null
     *
     * @throws ApiException
     */
    public function fromJsonToInteger(string $json)
    {
        try {
            return $this->fromTextToInteger($json);
        } catch (ApiException $e) {
            throw new ApiException(
                "Cannot cast JSON to integer",
                $e->getCode(),
                $e->getProcessor(),
                $e->getHtmlCode()
            );
        }
    }

    /**
     * Convert text to an integer.
     *
     * @param string $text
     *
     * @return int|float|null
     *
     * @throws ApiException
     */
    public function fromTextToInteger(string $text)
    {
        $text = trim($text, '"');
        if (strtolower($text) == 'nan') {
            return NAN;
        } elseif (strtolower($text) == 'null') {
            return null;
        } elseif (strtolower($text) == '-infinity' || strtolower($text) == '-inf') {
            return -INF;
        } elseif (strtolower($text) == 'infinity' || strtolower($text) == 'inf') {
            return INF;
        }
        if ($text != '0') {
            $text = preg_replace('/^0*/', '', $text);
        }
        $integer = filter_var($text, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if ($integer === null) {
            throw new ApiException("Cannot cast text to integer", 6, -1, 400);
        }
        return $integer;
    }

    /**
     * Convert undefined to integer.
     *
     * @param $data
     *
     * @return null
     */
    public function fromUndefinedToInteger($data)
    {
        return null;
    }

    /**
     * Convert an XML string to an integer.
     *
     * @param string $xml
     *
     * @throws ApiException
     */
    public function fromXmlToInteger(string $xml)
    {
        throw new ApiException('Cannot cast XML to integer', 6, -1, 400);
    }
}
