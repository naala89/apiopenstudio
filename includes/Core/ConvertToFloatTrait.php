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
     * Convert empty to float.
     *
     * @param $data
     *
     * @return float|null
     */
    public function fromEmptyToFloat($data): ?float
    {
        return null;
    }

    /**
     * Convert a boolean to a float.
     *
     * @param $data
     *
     * @return float
     *
     * @throws ApiException
     */
    public function fromBooleanToFloat($data): float
    {
        $result = filter_var($data, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
        if ($result === null) {
            throw new ApiException('Failed to convert boolean to float');
        }
        return $result;
    }

    /**
     * Convert an integer to a float.
     *
     * @param $data
     *
     * @return float
     */
    public function fromIntegerToFloat($data): float
    {
        return filter_var($data, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Convert a float to a float.
     *
     * @param $data
     *
     * @return float
     */
    public function fromFloatToFloat($data): float
    {
        return $data;
    }

    /**
     * Convert text to a float.
     *
     * @param $data
     *
     * @return float
     *
     * @throws ApiException
     */
    public function fromTextToFloat($data): float
    {
        $result = filter_var($data, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
        if ($result === null) {
            throw new ApiException('Failed to convert text to float');
        }
        return $result;
    }

    /**
     * Convert an array to a float.
     *
     * @param $data
     *
     * @return float
     *
     * @throws ApiException
     */
    public function fromArrayToFloat($data): float
    {
        throw new ApiException('Cannot cast array to float');
    }

    /**
     * Convert a JSON string to a float.
     *
     * @param $data
     *
     * @return float
     */
    public function fromJsonToFloat($data): float
    {
        return filter_var($data, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Convert an XML string to a float.
     *
     * @param $data
     *
     * @return float
     *
     * @throws ApiException
     */
    public function fromXmlToFloat($data): float
    {
        throw new ApiException('Cannot cast XML to float');
    }

    /**
     * Convert an HTML string to a float.
     *
     * @param $data
     *
     * @return float
     *
     * @throws ApiException
     */
    public function fromHtmlToFloat($data): float
    {
        throw new ApiException('Cannot cast HTML to float');
    }

    /**
     * Convert an image to a float.
     *
     * @param $data
     *
     * @return float
     *
     * @throws ApiException
     */
    public function fromImageToFloat($data): float
    {
        throw new ApiException('Cannot cast image to float');
    }

    /**
     * Convert a file to a float.
     *
     * @param $data
     *
     * @return float
     *
     * @throws ApiException
     */
    public function fromFileToFloat($data): float
    {
        throw new ApiException('Cannot cast file to float');
    }
}
