<?php

/**
 * Trait ConvertToBooleanTrait.
 *
 * @package    ApiOpenStudio
 * @subpackage Core
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
     * Convert empty to boolean.
     *
     * @param $data
     *
     * @return null
     */
    public function fromEmptyToBoolean($data)
    {
        return null;
    }

    /**
     * Convert boolean to boolean.
     *
     * @param $data
     *
     * @return boolean
     */
    public function fromBooleanToBoolean($data): bool
    {
        return $data;
    }

    /**
     * Convert integer to boolean.
     *
     * @param $data
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function fromIntegerToBoolean($data): bool
    {
        $result = filter_var($data, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($result === null) {
            throw new ApiException('Failed to convert integer to boolean');
        }
        return $result;
    }

    /**
     * Convert float to boolean.
     *
     * @param $data
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function fromFloatToBoolean($data): bool
    {
        $result = filter_var($data, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($result === null) {
            throw new ApiException('Failed to convert float to boolean');
        }
        return $result;
    }

    /**
     * Convert text to boolean.
     *
     * @param $data
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function fromTextToBoolean($data): bool
    {
        $result = filter_var($data, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($result === null) {
            throw new ApiException('Failed to convert text to boolean');
        }
        return $result;
    }

    /**
     * Convert array to boolean.
     *
     * @param $data
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function fromArrayToBoolean($data): bool
    {
        throw new ApiException('Cannot cast array to boolean');
    }

    /**
     * Convert JSON to boolean.
     *
     * @param $data
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function fromJsonToBoolean($data): bool
    {
        $result = filter_var($data, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($result === null) {
            throw new ApiException('Failed to convert integer to boolean');
        }
        return $result;
    }

    /**
     * Convert XML to boolean.
     *
     * @param $data
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function fromXmlToBoolean($data): bool
    {
        throw new ApiException('Cannot cast XML to boolean');
    }

    /**
     * Convert HTML to boolean.
     *
     * @param $data
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function fromHtmlToBoolean($data): bool
    {
        throw new ApiException('Cannot cast HTML to boolean');
    }

    /**
     * Convert image to boolean.
     *
     * @param $data
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function fromImageToBoolean($data): bool
    {
        throw new ApiException('Cannot cast image to boolean');
    }

    /**
     * Convert file to boolean.
     *
     * @param $data
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function fromFileToBoolean($data): bool
    {
        throw new ApiException('Cannot cast file to boolean');
    }
}
