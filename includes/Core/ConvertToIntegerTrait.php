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
     * Convert empty to integer.
     *
     * @param $data
     *
     * @return int|null
     */
    public function fromEmptyToInteger($data): ?int
    {
        return null;
    }

    /**
     * Convert a boolean to an integer.
     *
     * @param $data
     *
     * @return int
     */
    public function fromBooleanToInteger($data): int
    {
        return (int) $data;
    }

    /**
     * Convert an integer to an integer.
     *
     * @param $data
     *
     * @return int
     *
     * @throws ApiException
     */
    public function fromIntegerToInteger($data): int
    {
        $result = filter_var($data, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if ($result === null) {
            throw new ApiException('Failed to convert float to integer');
        }
        return $result;
    }

    /**
     * Convert a float to an integer.
     *
     * @param $data
     *
     * @return int
     *
     * @throws ApiException
     */
    public function fromFloatToInteger($data): int
    {
        $result = filter_var($data, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if ($result === null) {
            throw new ApiException('Failed to convert float to integer');
        }
        return $result;
    }

    /**
     * Convert text to an integer.
     *
     * @param $data
     *
     * @return int
     *
     * @throws ApiException
     */
    public function fromTextToInteger($data): int
    {
        $result = filter_var($data, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if ($result === null) {
            throw new ApiException('Failed to convert text to integer');
        }
        return $result;
    }

    /**
     * Convert a array to an integer.
     *
     * @param $data
     *
     * @return int
     *
     * @throws ApiException
     */
    public function fromArrayToInteger($data): int
    {
        throw new ApiException('Cannot cast array to integer');
    }

    /**
     * Convert a JSON string to an integer.
     *
     * @param $data
     *
     * @return int
     *
     * @throws ApiException
     */
    public function fromJsonToInteger($data): int
    {
        $result = filter_var($data, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if ($result === null) {
            throw new ApiException('Failed to convert JSON to integer');
        }
        return $result;
    }

    /**
     * Convert an XML string to an integer.
     *
     * @param $data
     *
     * @return int
     *
     * @throws ApiException
     */
    public function fromXmlToInteger($data): int
    {
        throw new ApiException('Cannot cast XML to integer');
    }

    /**
     * Convert an HTML string to an integer.
     *
     * @param $data
     *
     * @return int
     *
     * @throws ApiException
     */
    public function fromHtmlToInteger($data): int
    {
        throw new ApiException('Cannot cast HTML to integer');
    }

    /**
     * Convert an image to an integer.
     *
     * @param $data
     *
     * @return int
     *
     * @throws ApiException
     */
    public function fromImageToInteger($data): int
    {
        throw new ApiException('Cannot cast image to integer');
    }

    /**
     * Convert a file to an integer.
     *
     * @param $data
     *
     * @return int
     *
     * @throws ApiException
     */
    public function fromFileToInteger($data): int
    {
        throw new ApiException('Cannot cast file to integer');
    }
}
