<?php

/**
 * Trait ConvertToFloatTrait.
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
 * Trait ConvertToFloatTrait.
 *
 * Trait to cast an input value to float.
 */
trait ConvertToFloatTrait
{
    /**
     * Convert an array to a float.
     *
     * @throws ApiException
     */
    public function fromArrayToFloat(array $array)
    {
        throw new ApiException('Cannot cast array to float', 6, -1, 400);
    }

    /**
     * Convert a boolean to a float.
     *
     * @param bool $boolean
     *
     * @return float
     */
    public function fromBooleanToFloat(bool $boolean): float
    {
        return $boolean ? 1.0 : 0.0;
    }

    /**
     * Convert empty to float.
     *
     * @param $data
     *
     * @return ?float
     */
    public function fromEmptyToFloat($data): ?float
    {
        return null;
    }

    /**
     * Convert a file to a float.
     *
     * @param $file
     *
     * @throws ApiException
     */
    public function fromFileToFloat($file)
    {
        throw new ApiException('Cannot cast file to float', 6, -1, 400);
    }

    /**
     * Convert a float to a float.
     *
     * @param float $float
     *
     * @return float
     */
    public function fromFloatToFloat(float $float): float
    {
        return $float;
    }

    /**
     * Convert an HTML string to a float.
     *
     * @param string $html
     *
     * @throws ApiException
     */
    public function fromHtmlToFloat(string $html)
    {
        throw new ApiException('Cannot cast HTML to float', 6, -1, 400);
    }

    /**
     * Convert an image to a float.
     *
     * @param $image
     *
     * @throws ApiException
     */
    public function fromImageToFloat($image)
    {
        throw new ApiException('Cannot cast image to float', 6, -1, 400);
    }

    /**
     * Convert an integer to a float.
     *
     * @param int $integer
     *
     * @return float
     */
    public function fromIntegerToFloat(int $integer): float
    {
        return filter_var($integer, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Convert a JSON string to a float.
     *
     * @param string $json
     *
     * @return float
     *
     * @throws ApiException
     */
    public function fromJsonToFloat(string $json): float
    {
        try {
            return $this->fromTextToFloat($json);
        } catch (ApiException $e) {
            throw new ApiException(
                "Cannot cast JSON to float",
                $e->getCode(),
                $e->getProcessor(),
                $e->getHtmlCode()
            );
        }
    }

    /**
     * Convert text to a float.
     *
     * @param string $text
     *
     * @return float|int|mixed|null
     *
     * @throws ApiException
     */
    public function fromTextToFloat(string $text)
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
        if ($text != "0" && strpos($text, '.') === false) {
            $text = preg_replace('/^0*/', '', $text);
        }
        $float = filter_var($text, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
        if ($float === null) {
            throw new ApiException("Cannot cast text to float", 6, -1, 400);
        }
        return $float;
    }

    /**
     * Convert an XML string to a float.
     *
     * @param string $xml
     *
     * @throws ApiException
     */
    public function fromXmlToFloat(string $xml)
    {
        throw new ApiException('Cannot cast XML to float', 6, -1, 400);
    }
}
